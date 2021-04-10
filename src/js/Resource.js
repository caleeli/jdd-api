class Resource {
    constructor(url, owner) {
        this.url = url;
        this.owner = owner ? owner : {
            "$set"(target, attribute, value) {
                target[attribute] = value;
            }
        };
    }
    array(params = {}, index = []) {
        this.index(params, index);
        return index;
    }
    row(dataOrId = null, params = {}, record = {}) {
        if (dataOrId instanceof Object) {
            this.load(null, dataOrId, params);
            return params;
        } else {
            this.load(dataOrId, params, record);
            return record;
        }
    }
    index(params = {}, index = null) {
        return this.get(params, index, this.url).then(response => response.data);
    }
    load(id = null, params = {}, record = null) {
        return this.get(params, record, id ? `${this.url}/${id}` : this.url).then(response => response.data.data);
    }
    refresh(record, params = {}, initial = []) {
        return record instanceof Array
            ? this.index(params, record.splice(0, record.length, ...(initial || [])) && record).then(data => data.data)
            : this.load(record.id, params, record);
    }
    get(params = {}, response = null, url = this.url) {
        return this.axios(response, {
            url,
            method: "get",
            params
        });
    }
    post(data = {}, response = null, url = this.url) {
        return this.axios(response, {
            url,
            method: "post",
            data: {data}
        });
    }

    /**
     * Usage:
     *  this.call(id, 'method', {param1:value, param2:value}, response)
     *  this.call(id, 'method', {param1:value, param2:value})
     *  this.call(id, 'method')
     *  this.call('method', {param1:value, param2:value})
     *  this.call('method')
     *
     * @param {*} id 
     * @param {*} method 
     * @param {*} parameters 
     * @param {*} result 
     */
    call(id, method = {}, parameters = {}, result = null) {
        if (typeof id === 'string' && method instanceof Object) {
            result = parameters;
            parameters = method;
            method = id;
            id = null;
        }
        return this.axios(result, {
            url: this.url + (id ? '/' + id : ''),
            method: "post",
            data: {
                call: { method, parameters }
            }
        }).then(response => {
            result instanceof Array
                ? result.push(...response.data.response)
                : (result instanceof Object ? this.assign(result, response.data.response) : null);
            return response.data.response;
        });
    }
    rowCall(id, method = {}, parameters = {}, response = {}) {
        if (typeof id === 'string' && method instanceof Object) {
            this.call(id, method, parameters, response);
            return parameters;
        } else {
            this.call(id, method, parameters, response);
            return response;
        }
    }
    arrayCall(id, method = {}, parameters = [], response = []) {
        if (typeof id === 'string' && method instanceof Object) {
            this.call(id, method, parameters, response);
            return parameters;
        } else {
            this.call(id, method, parameters, response);
            return response;
        }
    }
    put(data = {}, response = null, url = this.url) {
        return this.axios(response, {
            url,
            method: "put",
            data: {data}
        });
    }
    patch(data = {}, response = null, url = this.url) {
        return this.axios(response, {
            url,
            method: "patch",
            data: {data}
        });
    }
    save(data = {}, response = null) {
        return this.put(data, response, `${this.url}/${data.id}`);
    }
    delete(dataOrId = null, response = null) {
        return this.axios(response, {
            url: dataOrId ? (this.url + '/' + (isNaN(dataOrId) ? dataOrId.id : dataOrId)) : this.url,
            method: "delete"
        });
    }
    assign(target, source) {
        Object.keys(source).forEach(attribute => {
            this.owner.$set(target, attribute, source[attribute]);
        });
    }
    axios(result, params) {
        return window.axios(params).then((response) => {
            response.data.data ? (
                result instanceof Array
                    ? result.push(...response.data.data)
                    : (result instanceof Object ? this.assign(result, response.data.data) : null)) : null;
            return response;
        });
    }
}

export default Resource;
