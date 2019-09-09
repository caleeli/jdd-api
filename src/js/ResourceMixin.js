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
 */
export default {
    beforeCreate() {
        const owner = this;
        this.$api = new Proxy({}, {
            get(resources, name) {
                return resources[name] ? resources[name] : (resources[name] = new Resource(name, owner));
            }
        });
    },
    data() {
        return {
            apiPrevIndex: {
            }
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
                        call ? this.$api[api].call(id, call, params).then(response => window._.set(this, data, response))
                            : this.$api[api].refresh(window._.get(this, data), params);
                    }
                }
                this.apiPrevIndex = JSON.parse(JSON.stringify(apiIndex === undefined ? {} : apiIndex));
            },
            deep: true,
            immediate: true
        }
    },
};
