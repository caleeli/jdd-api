module.exports =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "fae3");
/******/ })
/************************************************************************/
/******/ ({

/***/ "f6fd":
/***/ (function(module, exports) {

// document.currentScript polyfill by Adam Miller

// MIT license

(function(document){
  var currentScript = "currentScript",
      scripts = document.getElementsByTagName('script'); // Live NodeList collection

  // If browser needs currentScript polyfill, add get currentScript() to the document object
  if (!(currentScript in document)) {
    Object.defineProperty(document, currentScript, {
      get: function(){

        // IE 6-10 supports script readyState
        // IE 10+ support stack trace
        try { throw new Error(); }
        catch (err) {

          // Find the second match for the "at" string to get file src url from stack.
          // Specifically works with the format of stack traces in IE.
          var i, res = ((/.*at [^\(]*\((.*):.+:.+\)$/ig).exec(err.stack) || [false])[1];

          // For all scripts on the page, if src matches or if ready state is interactive, return the script tag
          for(i in scripts){
            if(scripts[i].src == res || scripts[i].readyState == "interactive"){
              return scripts[i];
            }
          }

          // If no match, return null
          return null;
        }
      }
    });
  }
})(document);


/***/ }),

/***/ "fae3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  if (true) {
    __webpack_require__("f6fd")
  }

  var i
  if ((i = window.document.currentScript) && (i = i.src.match(/(.+\/)[^/]+\.js(\?.*)?$/))) {
    __webpack_require__.p = i[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// CONCATENATED MODULE: ./src/js/Resource.js
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
    return record instanceof Array ? this.index(params, record.splice(0, record.length, ...(initial || [])) && record).then(data => data.data) : this.load(record.id, params, record);
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
      data: {
        data
      }
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
        call: {
          method,
          parameters
        }
      }
    }).then(response => {
      result instanceof Array ? result.push(...response.data.response) : result instanceof Object ? this.assign(result, response.data.response) : null;
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
      data: {
        data
      }
    });
  }

  patch(data = {}, response = null, url = this.url) {
    return this.axios(response, {
      url,
      method: "patch",
      data: {
        data
      }
    });
  }

  save(data = {}, response = null) {
    return this.put(data, response, `${this.url}/${data.id}`);
  }

  delete(dataOrId = null, response = null) {
    return this.axios(response, {
      url: dataOrId ? this.url + '/' + (isNaN(dataOrId) ? dataOrId.id : dataOrId) : this.url,
      method: "delete"
    });
  }

  assign(target, source) {
    Object.keys(source).forEach(attribute => {
      this.owner.$set(target, attribute, source[attribute]);
    });
  }

  axios(result, params) {
    return window.axios(params).then(response => {
      response.data.data ? result instanceof Array ? result.push(...response.data.data) : result instanceof Object ? this.assign(result, response.data.data) : null : null;
      return response;
    });
  }

}

/* harmony default export */ var js_Resource = (Resource);
// CONCATENATED MODULE: ./src/js/ResourceMixin.js

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
// Reserved names (by Vue/debugger)

const reserved = ['_isVue', '_vm', 'toJSON', 'state', 'render'];
const ResourceHandler = {
  get(resource, index) {
    if (typeof index === 'symbol' || reserved.includes(index)) {
      return undefined;
    }

    if (resource[index] !== undefined) {
      return resource[index];
    }

    return buildResource(index, resource, resource.owner);
  }

};

function buildResource(index, base = null, owner = null) {
  const url = base ? `${base.url}/${index}` : index;
  return new Proxy(new js_Resource(url, owner), ResourceHandler);
}

/* harmony default export */ var ResourceMixin = ({
  beforeCreate() {
    const owner = this;
    this.$api = new Proxy({}, {
      get(resources, name) {
        return resources[name] ? resources[name] : resources[name] = buildResource(name, null, owner);
      }

    });
  },

  data() {
    return {
      apiPrevIndex: {},
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
            (call ? this.$api[api].call(id, call, params).then(response => window._.set(this, data, response)) : this.$api[api].refresh(window._.get(this, data), params)).then(response => {
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
  }
});
// CONCATENATED MODULE: ./src/index.js


window.Resource = js_Resource;
window.ResourceMixin = ResourceMixin;
// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib-no-default.js




/***/ })

/******/ });
//# sourceMappingURL=index.common.js.map