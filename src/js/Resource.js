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
    row(id, params = {}, record = {}) {
        this.load(id, params, record);
        return record;
    }
    index(params = {}, index = null) {
        return this.get(this.url, params, index);
    }
    load(id, params = {}, record = null) {
        return this.get(this.url + '/' + id, params, record);
    }
    refresh(record, params = {}) {
        return record instanceof Array ? this.index(params, record) : this.load(record.id, params, record);
    }
    get(url, params = {}, response = null) {
        return this.axios(response, {
            url,
            method: "get",
            params
        });
    }
    post(data = {}, response = null) {
        return this.axios(response, {
            url: this.url,
            method: "post",
            data: data
        });
    }
    put(data = {}, response = null) {
        return this.axios(response, {
            url: this.url + '/' + data.id,
            method: "put",
            data: data
        });
    }
    save(data = {}, response = null) {
        return this.put(data, response);
    }
    delete(dataOrId, response = null) {
        return this.axios(response, {
            url: this.url + '/' + (isNaN(dataOrId) ? dataOrId.id : dataOrId),
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
                    ? result.splice(0, result.length, ...response.data.data)
                    : (result instanceof Object ? this.assign(result, response.data.data) : null)) : null;
            return response;
        });
    }
}

export default Resource;
