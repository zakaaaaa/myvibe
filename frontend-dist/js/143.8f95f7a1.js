"use strict";(self["webpackChunkMyVibe"]=self["webpackChunkMyVibe"]||[]).push([[143],{2524:function(e,t,n){n.r(t),n.d(t,{FirebaseMessagingWeb:function(){return Wt}});var i=n(2717),a=(n(4114),n(6573),n(8100),n(7936),n(7467),n(4732),n(9577),n(1454),n(4979),n(7642),n(8004),n(3853),n(5876),n(2475),n(5024),n(1698),n(5894)),o=n(852),r=n(4046),s=n(8071);const c="@firebase/installations",u="0.6.11",d=1e4,f=`w:${u}`,p="FIS_v2",l="https://firebaseinstallations.googleapis.com/v1",g=36e5,w="installations",h="Installations",m={["missing-app-config-values"]:'Missing App configuration value: "{$valueName}"',["not-registered"]:"Firebase Installation is not registered.",["installation-not-found"]:"Firebase Installation not found.",["request-failed"]:'{$requestName} request failed with error "{$serverCode} {$serverStatus}: {$serverMessage}"',["app-offline"]:"Could not process request. Application offline.",["delete-pending-registration"]:"Can't delete installation while there is a pending registration request."},b=new r.FA(w,h,m);function v(e){return e instanceof r.g&&e.code.includes("request-failed")}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function y({projectId:e}){return`${l}/projects/${e}/installations`}function k(e){return{token:e.token,requestStatus:2,expiresIn:P(e.expiresIn),creationTime:Date.now()}}async function S(e,t){const n=await t.json(),i=n.error;return b.create("request-failed",{requestName:e,serverCode:i.code,serverMessage:i.message,serverStatus:i.status})}function T({apiKey:e}){return new Headers({"Content-Type":"application/json",Accept:"application/json","x-goog-api-key":e})}function I(e,{refreshToken:t}){const n=T(e);return n.append("Authorization",j(t)),n}async function C(e){const t=await e();return t.status>=500&&t.status<600?e():t}function P(e){return Number(e.replace("s","000"))}function j(e){return`${p} ${e}`}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function E({appConfig:e,heartbeatServiceProvider:t},{fid:n}){const i=y(e),a=T(e),o=t.getImmediate({optional:!0});if(o){const e=await o.getHeartbeatsHeader();e&&a.append("x-firebase-client",e)}const r={fid:n,authVersion:p,appId:e.appId,sdkVersion:f},s={method:"POST",headers:a,body:JSON.stringify(r)},c=await C((()=>fetch(i,s)));if(c.ok){const e=await c.json(),t={fid:e.fid||n,registrationStatus:2,refreshToken:e.refreshToken,authToken:k(e.authToken)};return t}throw await S("Create Installation",c)}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function O(e){return new Promise((t=>{setTimeout(t,e)}))}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function K(e){const t=btoa(String.fromCharCode(...e));return t.replace(/\+/g,"-").replace(/\//g,"_")}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const A=/^[cdef][\w-]{21}$/,D="";function N(){try{const e=new Uint8Array(17),t=self.crypto||self.msCrypto;t.getRandomValues(e),e[0]=112+e[0]%16;const n=M(e);return A.test(n)?n:D}catch(e){return D}}function M(e){const t=K(e);return t.substr(0,22)}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function _(e){return`${e.appName}!${e.appId}`}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const R=new Map;function $(e,t){const n=_(e);q(n,t),F(n,t)}function q(e,t){const n=R.get(e);if(n)for(const i of n)i(t)}function F(e,t){const n=H();n&&n.postMessage({key:e,fid:t}),U()}let x=null;function H(){return!x&&"BroadcastChannel"in self&&(x=new BroadcastChannel("[Firebase] FID Change"),x.onmessage=e=>{q(e.data.key,e.data.fid)}),x}function U(){0===R.size&&x&&(x.close(),x=null)}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const W="firebase-installations-database",L=1,V="firebase-installations-store";let B=null;function z(){return B||(B=(0,s.P2)(W,L,{upgrade:(e,t)=>{switch(t){case 0:e.createObjectStore(V)}}})),B}async function G(e,t){const n=_(e),i=await z(),a=i.transaction(V,"readwrite"),o=a.objectStore(V),r=await o.get(n);return await o.put(t,n),await a.done,r&&r.fid===t.fid||$(e,t.fid),t}async function J(e){const t=_(e),n=await z(),i=n.transaction(V,"readwrite");await i.objectStore(V).delete(t),await i.done}async function Y(e,t){const n=_(e),i=await z(),a=i.transaction(V,"readwrite"),o=a.objectStore(V),r=await o.get(n),s=t(r);return void 0===s?await o.delete(n):await o.put(s,n),await a.done,!s||r&&r.fid===s.fid||$(e,s.fid),s}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Q(e){let t;const n=await Y(e.appConfig,(n=>{const i=X(n),a=Z(e,i);return t=a.registrationPromise,a.installationEntry}));return n.fid===D?{installationEntry:await t}:{installationEntry:n,registrationPromise:t}}function X(e){const t=e||{fid:N(),registrationStatus:0};return ie(t)}function Z(e,t){if(0===t.registrationStatus){if(!navigator.onLine){const e=Promise.reject(b.create("app-offline"));return{installationEntry:t,registrationPromise:e}}const n={fid:t.fid,registrationStatus:1,registrationTime:Date.now()},i=ee(e,n);return{installationEntry:n,registrationPromise:i}}return 1===t.registrationStatus?{installationEntry:t,registrationPromise:te(e)}:{installationEntry:t}}async function ee(e,t){try{const n=await E(e,t);return G(e.appConfig,n)}catch(n){throw v(n)&&409===n.customData.serverCode?await J(e.appConfig):await G(e.appConfig,{fid:t.fid,registrationStatus:0}),n}}async function te(e){let t=await ne(e.appConfig);while(1===t.registrationStatus)await O(100),t=await ne(e.appConfig);if(0===t.registrationStatus){const{installationEntry:t,registrationPromise:n}=await Q(e);return n||t}return t}function ne(e){return Y(e,(e=>{if(!e)throw b.create("installation-not-found");return ie(e)}))}function ie(e){return ae(e)?{fid:e.fid,registrationStatus:0}:e}function ae(e){return 1===e.registrationStatus&&e.registrationTime+d<Date.now()}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function oe({appConfig:e,heartbeatServiceProvider:t},n){const i=re(e,n),a=I(e,n),o=t.getImmediate({optional:!0});if(o){const e=await o.getHeartbeatsHeader();e&&a.append("x-firebase-client",e)}const r={installation:{sdkVersion:f,appId:e.appId}},s={method:"POST",headers:a,body:JSON.stringify(r)},c=await C((()=>fetch(i,s)));if(c.ok){const e=await c.json(),t=k(e);return t}throw await S("Generate Auth Token",c)}function re(e,{fid:t}){return`${y(e)}/${t}/authTokens:generate`}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function se(e,t=!1){let n;const i=await Y(e.appConfig,(i=>{if(!fe(i))throw b.create("not-registered");const a=i.authToken;if(!t&&pe(a))return i;if(1===a.requestStatus)return n=ce(e,t),i;{if(!navigator.onLine)throw b.create("app-offline");const t=ge(i);return n=de(e,t),t}})),a=n?await n:i.authToken;return a}async function ce(e,t){let n=await ue(e.appConfig);while(1===n.authToken.requestStatus)await O(100),n=await ue(e.appConfig);const i=n.authToken;return 0===i.requestStatus?se(e,t):i}function ue(e){return Y(e,(e=>{if(!fe(e))throw b.create("not-registered");const t=e.authToken;return we(t)?Object.assign(Object.assign({},e),{authToken:{requestStatus:0}}):e}))}async function de(e,t){try{const n=await oe(e,t),i=Object.assign(Object.assign({},t),{authToken:n});return await G(e.appConfig,i),n}catch(n){if(!v(n)||401!==n.customData.serverCode&&404!==n.customData.serverCode){const n=Object.assign(Object.assign({},t),{authToken:{requestStatus:0}});await G(e.appConfig,n)}else await J(e.appConfig);throw n}}function fe(e){return void 0!==e&&2===e.registrationStatus}function pe(e){return 2===e.requestStatus&&!le(e)}function le(e){const t=Date.now();return t<e.creationTime||e.creationTime+e.expiresIn<t+g}function ge(e){const t={requestStatus:1,requestTime:Date.now()};return Object.assign(Object.assign({},e),{authToken:t})}function we(e){return 1===e.requestStatus&&e.requestTime+d<Date.now()}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function he(e){const t=e,{installationEntry:n,registrationPromise:i}=await Q(t);return i?i.catch(console.error):se(t).catch(console.error),n.fid}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function me(e,t=!1){const n=e;await be(n);const i=await se(n,t);return i.token}async function be(e){const{registrationPromise:t}=await Q(e);t&&await t}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function ve(e){if(!e||!e.options)throw ye("App Configuration");if(!e.name)throw ye("App Name");const t=["projectId","apiKey","appId"];for(const n of t)if(!e.options[n])throw ye(n);return{appName:e.name,projectId:e.options.projectId,apiKey:e.options.apiKey,appId:e.options.appId}}function ye(e){return b.create("missing-app-config-values",{valueName:e})}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const ke="installations",Se="installations-internal",Te=e=>{const t=e.getProvider("app").getImmediate(),n=ve(t),i=(0,a.j6)(t,"heartbeat"),o={app:t,appConfig:n,heartbeatServiceProvider:i,_delete:()=>Promise.resolve()};return o},Ie=e=>{const t=e.getProvider("app").getImmediate(),n=(0,a.j6)(t,ke).getImmediate(),i={getId:()=>he(n),getToken:e=>me(n,e)};return i};function Ce(){(0,a.om)(new o.uA(ke,Te,"PUBLIC")),(0,a.om)(new o.uA(Se,Ie,"PRIVATE"))}Ce(),(0,a.KO)(c,u),(0,a.KO)(c,u,"esm2017");
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
const Pe="/firebase-messaging-sw.js",je="/firebase-cloud-messaging-push-scope",Ee="BDOU99-h67HcA6JeFXHbSNMu7e2yNNu3RzoMj8TM4W88jITfq7ZmPvIM1Iv-4_l2LxQcYwhqby2xGpWwzjfAnG4",Oe="https://fcmregistrations.googleapis.com/v1",Ke="google.c.a.c_id",Ae="google.c.a.c_l",De="google.c.a.ts",Ne="google.c.a.e",Me=1e4;var _e,Re;
/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function $e(e){const t=new Uint8Array(e),n=btoa(String.fromCharCode(...t));return n.replace(/=/g,"").replace(/\+/g,"-").replace(/\//g,"_")}function qe(e){const t="=".repeat((4-e.length%4)%4),n=(e+t).replace(/\-/g,"+").replace(/_/g,"/"),i=atob(n),a=new Uint8Array(i.length);for(let o=0;o<i.length;++o)a[o]=i.charCodeAt(o);return a}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */(function(e){e[e["DATA_MESSAGE"]=1]="DATA_MESSAGE",e[e["DISPLAY_NOTIFICATION"]=3]="DISPLAY_NOTIFICATION"})(_e||(_e={})),function(e){e["PUSH_RECEIVED"]="push-received",e["NOTIFICATION_CLICKED"]="notification-clicked"}(Re||(Re={}));const Fe="fcm_token_details_db",xe=5,He="fcm_token_object_Store";async function Ue(e){if("databases"in indexedDB){const e=await indexedDB.databases(),t=e.map((e=>e.name));if(!t.includes(Fe))return null}let t=null;const n=await(0,s.P2)(Fe,xe,{upgrade:async(n,i,a,o)=>{var r;if(i<2)return;if(!n.objectStoreNames.contains(He))return;const s=o.objectStore(He),c=await s.index("fcmSenderId").get(e);if(await s.clear(),c)if(2===i){const e=c;if(!e.auth||!e.p256dh||!e.endpoint)return;t={token:e.fcmToken,createTime:null!==(r=e.createTime)&&void 0!==r?r:Date.now(),subscriptionOptions:{auth:e.auth,p256dh:e.p256dh,endpoint:e.endpoint,swScope:e.swScope,vapidKey:"string"===typeof e.vapidKey?e.vapidKey:$e(e.vapidKey)}}}else if(3===i){const e=c;t={token:e.fcmToken,createTime:e.createTime,subscriptionOptions:{auth:$e(e.auth),p256dh:$e(e.p256dh),endpoint:e.endpoint,swScope:e.swScope,vapidKey:$e(e.vapidKey)}}}else if(4===i){const e=c;t={token:e.fcmToken,createTime:e.createTime,subscriptionOptions:{auth:$e(e.auth),p256dh:$e(e.p256dh),endpoint:e.endpoint,swScope:e.swScope,vapidKey:$e(e.vapidKey)}}}}});return n.close(),await(0,s.MR)(Fe),await(0,s.MR)("fcm_vapid_details_db"),await(0,s.MR)("undefined"),We(t)?t:null}function We(e){if(!e||!e.subscriptionOptions)return!1;const{subscriptionOptions:t}=e;return"number"===typeof e.createTime&&e.createTime>0&&"string"===typeof e.token&&e.token.length>0&&"string"===typeof t.auth&&t.auth.length>0&&"string"===typeof t.p256dh&&t.p256dh.length>0&&"string"===typeof t.endpoint&&t.endpoint.length>0&&"string"===typeof t.swScope&&t.swScope.length>0&&"string"===typeof t.vapidKey&&t.vapidKey.length>0}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Le="firebase-messaging-database",Ve=1,Be="firebase-messaging-store";let ze=null;function Ge(){return ze||(ze=(0,s.P2)(Le,Ve,{upgrade:(e,t)=>{switch(t){case 0:e.createObjectStore(Be)}}})),ze}async function Je(e){const t=Xe(e),n=await Ge(),i=await n.transaction(Be).objectStore(Be).get(t);if(i)return i;{const t=await Ue(e.appConfig.senderId);if(t)return await Ye(e,t),t}}async function Ye(e,t){const n=Xe(e),i=await Ge(),a=i.transaction(Be,"readwrite");return await a.objectStore(Be).put(t,n),await a.done,t}async function Qe(e){const t=Xe(e),n=await Ge(),i=n.transaction(Be,"readwrite");await i.objectStore(Be).delete(t),await i.done}function Xe({appConfig:e}){return e.appId}
/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const Ze={["missing-app-config-values"]:'Missing App configuration value: "{$valueName}"',["only-available-in-window"]:"This method is available in a Window context.",["only-available-in-sw"]:"This method is available in a service worker context.",["permission-default"]:"The notification permission was not granted and dismissed instead.",["permission-blocked"]:"The notification permission was not granted and blocked instead.",["unsupported-browser"]:"This browser doesn't support the API's required to use the Firebase SDK.",["indexed-db-unsupported"]:"This browser doesn't support indexedDb.open() (ex. Safari iFrame, Firefox Private Browsing, etc)",["failed-service-worker-registration"]:"We are unable to register the default service worker. {$browserErrorMessage}",["token-subscribe-failed"]:"A problem occurred while subscribing the user to FCM: {$errorInfo}",["token-subscribe-no-token"]:"FCM returned no token when subscribing the user to push.",["token-unsubscribe-failed"]:"A problem occurred while unsubscribing the user from FCM: {$errorInfo}",["token-update-failed"]:"A problem occurred while updating the user from FCM: {$errorInfo}",["token-update-no-token"]:"FCM returned no token when updating the user to push.",["use-sw-after-get-token"]:"The useServiceWorker() method may only be called once and must be called before calling getToken() to ensure your service worker is used.",["invalid-sw-registration"]:"The input to useServiceWorker() must be a ServiceWorkerRegistration.",["invalid-bg-handler"]:"The input to setBackgroundMessageHandler() must be a function.",["invalid-vapid-key"]:"The public VAPID key must be a string.",["use-vapid-key-after-get-token"]:"The usePublicVapidKey() method may only be called once and must be called before calling getToken() to ensure your VAPID key is used."},et=new r.FA("messaging","Messaging",Ze);
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
async function tt(e,t){const n=await ot(e),i=rt(t),a={method:"POST",headers:n,body:JSON.stringify(i)};let o;try{const t=await fetch(at(e.appConfig),a);o=await t.json()}catch(r){throw et.create("token-subscribe-failed",{errorInfo:null===r||void 0===r?void 0:r.toString()})}if(o.error){const e=o.error.message;throw et.create("token-subscribe-failed",{errorInfo:e})}if(!o.token)throw et.create("token-subscribe-no-token");return o.token}async function nt(e,t){const n=await ot(e),i=rt(t.subscriptionOptions),a={method:"PATCH",headers:n,body:JSON.stringify(i)};let o;try{const n=await fetch(`${at(e.appConfig)}/${t.token}`,a);o=await n.json()}catch(r){throw et.create("token-update-failed",{errorInfo:null===r||void 0===r?void 0:r.toString()})}if(o.error){const e=o.error.message;throw et.create("token-update-failed",{errorInfo:e})}if(!o.token)throw et.create("token-update-no-token");return o.token}async function it(e,t){const n=await ot(e),i={method:"DELETE",headers:n};try{const n=await fetch(`${at(e.appConfig)}/${t}`,i),a=await n.json();if(a.error){const e=a.error.message;throw et.create("token-unsubscribe-failed",{errorInfo:e})}}catch(a){throw et.create("token-unsubscribe-failed",{errorInfo:null===a||void 0===a?void 0:a.toString()})}}function at({projectId:e}){return`${Oe}/projects/${e}/registrations`}async function ot({appConfig:e,installations:t}){const n=await t.getToken();return new Headers({"Content-Type":"application/json",Accept:"application/json","x-goog-api-key":e.apiKey,"x-goog-firebase-installations-auth":`FIS ${n}`})}function rt({p256dh:e,auth:t,endpoint:n,vapidKey:i}){const a={web:{endpoint:n,auth:t,p256dh:e}};return i!==Ee&&(a.web.applicationPubKey=i),a}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */const st=6048e5;async function ct(e){const t=await pt(e.swRegistration,e.vapidKey),n={vapidKey:e.vapidKey,swScope:e.swRegistration.scope,endpoint:t.endpoint,auth:$e(t.getKey("auth")),p256dh:$e(t.getKey("p256dh"))},i=await Je(e.firebaseDependencies);if(i){if(lt(i.subscriptionOptions,n))return Date.now()>=i.createTime+st?dt(e,{token:i.token,createTime:Date.now(),subscriptionOptions:n}):i.token;try{await it(e.firebaseDependencies,i.token)}catch(a){console.warn(a)}return ft(e.firebaseDependencies,n)}return ft(e.firebaseDependencies,n)}async function ut(e){const t=await Je(e.firebaseDependencies);t&&(await it(e.firebaseDependencies,t.token),await Qe(e.firebaseDependencies));const n=await e.swRegistration.pushManager.getSubscription();return!n||n.unsubscribe()}async function dt(e,t){try{const n=await nt(e.firebaseDependencies,t),i=Object.assign(Object.assign({},t),{token:n,createTime:Date.now()});return await Ye(e.firebaseDependencies,i),n}catch(n){throw n}}async function ft(e,t){const n=await tt(e,t),i={token:n,createTime:Date.now(),subscriptionOptions:t};return await Ye(e,i),i.token}async function pt(e,t){const n=await e.pushManager.getSubscription();return n||e.pushManager.subscribe({userVisibleOnly:!0,applicationServerKey:qe(t)})}function lt(e,t){const n=t.vapidKey===e.vapidKey,i=t.endpoint===e.endpoint,a=t.auth===e.auth,o=t.p256dh===e.p256dh;return n&&i&&a&&o}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function gt(e){const t={from:e.from,collapseKey:e.collapse_key,messageId:e.fcmMessageId};return wt(t,e),ht(t,e),mt(t,e),t}function wt(e,t){if(!t.notification)return;e.notification={};const n=t.notification.title;n&&(e.notification.title=n);const i=t.notification.body;i&&(e.notification.body=i);const a=t.notification.image;a&&(e.notification.image=a);const o=t.notification.icon;o&&(e.notification.icon=o)}function ht(e,t){t.data&&(e.data=t.data)}function mt(e,t){var n,i,a,o,r;if(!t.fcmOptions&&!(null===(n=t.notification)||void 0===n?void 0:n.click_action))return;e.fcmOptions={};const s=null!==(a=null===(i=t.fcmOptions)||void 0===i?void 0:i.link)&&void 0!==a?a:null===(o=t.notification)||void 0===o?void 0:o.click_action;s&&(e.fcmOptions.link=s);const c=null===(r=t.fcmOptions)||void 0===r?void 0:r.analytics_label;c&&(e.fcmOptions.analyticsLabel=c)}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function bt(e){return"object"===typeof e&&!!e&&Ke in e}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function vt(e,t){const n=[];for(let i=0;i<e.length;i++)n.push(e.charAt(i)),i<t.length&&n.push(t.charAt(i));return n.join("")}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function yt(e){if(!e||!e.options)throw kt("App Configuration Object");if(!e.name)throw kt("App Name");const t=["projectId","apiKey","appId","messagingSenderId"],{options:n}=e;for(const i of t)if(!n[i])throw kt(i);return{appName:e.name,projectId:n.projectId,apiKey:n.apiKey,appId:n.appId,senderId:n.messagingSenderId}}function kt(e){return et.create("missing-app-config-values",{valueName:e})}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */vt("AzSCbw63g1R0nCw85jG8","Iaya3yLKwmgvh7cF0q4");class St{constructor(e,t,n){this.deliveryMetricsExportedToBigQueryEnabled=!1,this.onBackgroundMessageHandler=null,this.onMessageHandler=null,this.logEvents=[],this.isLogServiceStarted=!1;const i=yt(e);this.firebaseDependencies={app:e,appConfig:i,installations:t,analyticsProvider:n}}_delete(){return Promise.resolve()}}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Tt(e){try{e.swRegistration=await navigator.serviceWorker.register(Pe,{scope:je}),e.swRegistration.update().catch((()=>{})),await It(e.swRegistration)}catch(t){throw et.create("failed-service-worker-registration",{browserErrorMessage:null===t||void 0===t?void 0:t.message})}}async function It(e){return new Promise(((t,n)=>{const i=setTimeout((()=>n(new Error(`Service worker not registered after ${Me} ms`))),Me),a=e.installing||e.waiting;e.active?(clearTimeout(i),t()):a?a.onstatechange=e=>{var n;"activated"===(null===(n=e.target)||void 0===n?void 0:n.state)&&(a.onstatechange=null,clearTimeout(i),t())}:(clearTimeout(i),n(new Error("No incoming service worker found.")))}))}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Ct(e,t){if(t||e.swRegistration||await Tt(e),t||!e.swRegistration){if(!(t instanceof ServiceWorkerRegistration))throw et.create("invalid-sw-registration");e.swRegistration=t}}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Pt(e,t){t?e.vapidKey=t:e.vapidKey||(e.vapidKey=Ee)}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function jt(e,t){if(!navigator)throw et.create("only-available-in-window");if("default"===Notification.permission&&await Notification.requestPermission(),"granted"!==Notification.permission)throw et.create("permission-blocked");return await Pt(e,null===t||void 0===t?void 0:t.vapidKey),await Ct(e,null===t||void 0===t?void 0:t.serviceWorkerRegistration),ct(e)}
/**
 * @license
 * Copyright 2019 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Et(e,t,n){const i=Ot(t),a=await e.firebaseDependencies.analyticsProvider.get();a.logEvent(i,{message_id:n[Ke],message_name:n[Ae],message_time:n[De],message_device_time:Math.floor(Date.now()/1e3)})}function Ot(e){switch(e){case Re.NOTIFICATION_CLICKED:return"notification_open";case Re.PUSH_RECEIVED:return"notification_foreground";default:throw new Error}}
/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Kt(e,t){const n=t.data;if(!n.isFirebaseMessaging)return;e.onMessageHandler&&n.messageType===Re.PUSH_RECEIVED&&("function"===typeof e.onMessageHandler?e.onMessageHandler(gt(n)):e.onMessageHandler.next(gt(n)));const i=n.data;bt(i)&&"1"===i[Ne]&&await Et(e,n.messageType,i)}const At="@firebase/messaging",Dt="0.12.15",Nt=e=>{const t=new St(e.getProvider("app").getImmediate(),e.getProvider("installations-internal").getImmediate(),e.getProvider("analytics-internal"));return navigator.serviceWorker.addEventListener("message",(e=>Kt(t,e))),t},Mt=e=>{const t=e.getProvider("messaging").getImmediate(),n={getToken:e=>jt(t,e)};return n};function _t(){(0,a.om)(new o.uA("messaging",Nt,"PUBLIC")),(0,a.om)(new o.uA("messaging-internal",Mt,"PRIVATE")),(0,a.KO)(At,Dt),(0,a.KO)(At,Dt,"esm2017")}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function Rt(){try{await(0,r.eX)()}catch(e){return!1}return"undefined"!==typeof window&&(0,r.zW)()&&(0,r.dM)()&&"serviceWorker"in navigator&&"PushManager"in window&&"Notification"in window&&"fetch"in window&&ServiceWorkerRegistration.prototype.hasOwnProperty("showNotification")&&PushSubscription.prototype.hasOwnProperty("getKey")}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */async function $t(e){if(!navigator)throw et.create("only-available-in-window");return e.swRegistration||await Tt(e),ut(e)}
/**
 * @license
 * Copyright 2020 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function qt(e,t){if(!navigator)throw et.create("only-available-in-window");return e.onMessageHandler=t,()=>{e.onMessageHandler=null}}
/**
 * @license
 * Copyright 2017 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */function Ft(e=(0,a.Sx)()){return Rt().then((e=>{if(!e)throw et.create("unsupported-browser")}),(e=>{throw et.create("indexed-db-unsupported")})),(0,a.j6)((0,r.Ku)(e),"messaging").getImmediate()}async function xt(e,t){return e=(0,r.Ku)(e),jt(e,t)}function Ht(e){return e=(0,r.Ku)(e),$t(e)}function Ut(e,t){return e=(0,r.Ku)(e),qt(e,t)}_t();class Wt extends i.E_{constructor(){super(),Rt().then((e=>{if(!e)return;const t=Ft();Ut(t,(e=>this.handleNotificationReceived(e)))}))}async checkPermissions(){const e=await Rt();if(!e)return{receive:"denied"};const t=this.convertNotificationPermissionToPermissionState(Notification.permission);return{receive:t}}async requestPermissions(){const e=await Rt();if(!e)return{receive:"denied"};const t=await Notification.requestPermission(),n=this.convertNotificationPermissionToPermissionState(t);return{receive:n}}async isSupported(){const e=await Rt();return{isSupported:e}}async getToken(e){const t=Ft(),n=await xt(t,{vapidKey:e.vapidKey,serviceWorkerRegistration:e.serviceWorkerRegistration});return{token:n}}async deleteToken(){const e=Ft();await Ht(e)}async getDeliveredNotifications(){this.throwUnavailableError()}async removeDeliveredNotifications(e){this.throwUnavailableError()}async removeAllDeliveredNotifications(){this.throwUnavailableError()}async subscribeToTopic(e){this.throwUnavailableError()}async unsubscribeFromTopic(e){this.throwUnavailableError()}async createChannel(e){this.throwUnavailableError()}async deleteChannel(e){this.throwUnavailableError()}async listChannels(){this.throwUnavailableError()}handleNotificationReceived(e){const t=this.createNotificationResult(e),n={notification:t};this.notifyListeners(Wt.notificationReceivedEvent,n)}createNotificationResult(e){var t,n,i;const a={body:null===(t=e.notification)||void 0===t?void 0:t.body,data:e.data,id:e.messageId,image:null===(n=e.notification)||void 0===n?void 0:n.image,title:null===(i=e.notification)||void 0===i?void 0:i.title};return a}convertNotificationPermissionToPermissionState(e){let t="prompt";switch(e){case"granted":t="granted";break;case"denied":t="denied";break}return t}throwUnavailableError(){throw this.unavailable("Not available on web.")}}Wt.notificationReceivedEvent="notificationReceived"}}]);
//# sourceMappingURL=143.8f95f7a1.js.map