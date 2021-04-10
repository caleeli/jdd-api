(()=>{"use strict";const t=class{constructor(t,e){this.url=t,this.owner=e||{$set(t,e,i){t[e]=i}}}array(t={},e=[]){return this.index(t,e),e}row(t=null,e={},i={}){return t instanceof Object?(this.load(null,t,e),e):(this.load(t,e,i),i)}index(t={},e=null){return this.get(t,e,this.url).then((t=>t.data))}load(t=null,e={},i=null){return this.get(e,i,t?`${this.url}/${t}`:this.url).then((t=>t.data.data))}refresh(t,e={},i=[]){return t instanceof Array?this.index(e,t.splice(0,t.length,...i||[])&&t).then((t=>t.data)):this.load(t.id,e,t)}get(t={},e=null,i=this.url){return this.axios(e,{url:i,method:"get",params:t})}post(t={},e=null,i=this.url){return this.axios(e,{url:i,method:"post",data:{data:t}})}call(t,e={},i={},a=null){return"string"==typeof t&&e instanceof Object&&(a=i,i=e,e=t,t=null),this.axios(a,{url:this.url+(t?"/"+t:""),method:"post",data:{call:{method:e,parameters:i}}}).then((t=>(a instanceof Array?a.push(...t.data.response):a instanceof Object&&this.assign(a,t.data.response),t.data.response)))}rowCall(t,e={},i={},a={}){return"string"==typeof t&&e instanceof Object?(this.call(t,e,i,a),i):(this.call(t,e,i,a),a)}arrayCall(t,e={},i=[],a=[]){return"string"==typeof t&&e instanceof Object?(this.call(t,e,i,a),i):(this.call(t,e,i,a),a)}put(t={},e=null,i=this.url){return this.axios(e,{url:i,method:"put",data:{data:t}})}patch(t={},e=null,i=this.url){return this.axios(e,{url:i,method:"patch",data:{data:t}})}save(t={},e=null){return this.put(t,e,`${this.url}/${t.id}`)}delete(t=null,e=null){return this.axios(e,{url:t?this.url+"/"+(isNaN(t)?t.id:t):this.url,method:"delete"})}assign(t,e){Object.keys(e).forEach((i=>{this.owner.$set(t,i,e[i])}))}axios(t,e){return window.axios(e).then((e=>(e.data.data&&(t instanceof Array?t.push(...e.data.data):t instanceof Object&&this.assign(t,e.data.data)),e)))}},e=["_isVue","_vm","toJSON","state","render"],i={get(t,i){if("symbol"!=typeof i&&!e.includes(i))return void 0!==t[i]?t[i]:a(i,t,t.owner)}};function a(e,a=null,s=null){const n=a?`${a.url}/${e}`:e;return new Proxy(new t(n,s),i)}const s={beforeCreate(){const t=this;this.$api=new Proxy({},{get:(e,i)=>e[i]?e[i]:e[i]=a(i,null,t)})},data:()=>({apiPrevIndex:{},apiIsRunning:!1}),watch:{apiIndex:{handler(t){for(let e in t){let i=JSON.stringify(t[e]);if(i!==JSON.stringify(void 0===this.apiPrevIndex[e]?null:this.apiPrevIndex[e])){let t=JSON.parse(i),a=t.$api?t.$api:e,s=t.$call?t.$call:null,n=t.$id?t.$id:null;delete t.$api,delete t.$call,delete t.$id,this.apiIsRunning=!0,(s?this.$api[a].call(n,s,t).then((t=>window._.set(this,e,t))):this.$api[a].refresh(window._.get(this,e),t)).then((t=>(this.apiIsRunning=!1,t)))}}this.apiPrevIndex=JSON.parse(JSON.stringify(void 0===t?{}:t))},deep:!0,immediate:!0}}};window.Resource=t,window.ResourceMixin=s})();