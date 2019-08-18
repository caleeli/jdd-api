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
                users: {},
                enabledUsers: {$api:'users', page:2},
            },
            users: [],
            enabledUsers: [],
            options: this.$api.options.array({page:1}),
            user: this.$api.users.row(1)
        };
    },
    watch: {
        apiIndex: {
            handler(apiIndex) {
                for(let data in apiIndex) {
                    let jParams = JSON.stringify(apiIndex[data]);
                    if (jParams !== JSON.stringify(this.apiPrevIndex[data])) {
                        let params = JSON.parse(jParams);
                        let api = params.$api ? params.$api : data;
                        delete params.$api;
                        this.$api[api].refresh(this[data], params);
                    }
                }
                this.apiPrevIndex = JSON.parse(JSON.stringify(apiIndex));
            },
            deep: true,
            immediate: true
        }
    },
};
