import Resource from './Resource';

/**
 * Usage:
 * 
 * {
 *  data() {
 *      return {
 *          apiIndex: {
 *              users: 
 *          }
 *      };
 *  }
 * }
 * 
 * this.$api.user[1].row() User with id=1
 * this.$api.user[1].roleObject.row() Role object of User with id=1
 * this.$api.user[1].roleObject.users.array() Users of RoleObject of User with id=1
 */

const ResourceHandler = {
    get(resource, index) {
        if (resource[index] !== undefined) {
            return resource[index];
        }
        return buildResource(index, resource, resource.owner);
    }
};

function buildResource(index, base = null, owner = null) {
    const url = base ? `${base.url}/${index}` : index;
    return new Proxy(new Resource(url, owner), ResourceHandler);
}

export default {
    beforeCreate() {
        const owner = this;
        this.$api = new Proxy({}, {
            get(resources, name) {
                return resources[name] ? resources[name] : (resources[name] = buildResource(name, null, owner));
            }
        });
    },
    data() {
        return {
            apiPrevIndex: {
            },
            apiIsRunning: false
        };
    },
    watch: {
        apiIndex: {
            handler(apiIndex) {
                for (let data in apiIndex) {
                    let jParams = JSON.stringify(apiIndex[data]);
                    if (jParams !== JSON.stringify(this.apiPrevIndex[data] === undefined ? null : this.apiPrevIndex[data])) {
                        let params = JSON.parse(jParams);
                        let api = params.$api ? params.$api : data;
                        let call = params.$call ? params.$call : null;
                        let id = params.$id ? params.$id : null;
                        delete params.$api;
                        delete params.$call;
                        delete params.$id;
                        this.apiIsRunning = true;
                        (call ? this.$api[api].call(id, call, params).then(response => window._.set(this, data, response))
                            : this.$api[api].refresh(window._.get(this, data), params)).then((response) => {
                                this.apiIsRunning = false;
                                return response;
                            });
                    }
                }
                this.apiPrevIndex = JSON.parse(JSON.stringify(apiIndex === undefined ? {} : apiIndex));
            },
            deep: true,
            immediate: true
        }
    },
};
