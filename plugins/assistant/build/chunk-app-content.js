"use strict";(self.webpackChunkfl_assistant=self.webpackChunkfl_assistant||[]).push([[870],{4046:(e,t,a)=>{a.r(t),a.d(t,{default:()=>_});var l=a(7363),n=a.n(l),o=a(8003),r=a(2974),s=a(6711),i=a(9818),u=a(4533),c=a(3882),d=a(4352);const m=e=>{let{type:t="post"}=e;const a="fl-content",{query:l,listStyle:r}=(0,i.useAppState)(a),{setQuery:s,setListStyle:c}=(0,i.getAppActions)(a),m=d.W.query,p=()=>{const e={title:(0,o.__)("Title"),author:(0,o.__)("Author"),ID:(0,o.__)("Post ID"),date:(0,o.__)("Date Created"),modified:(0,o.__)("Date Modified")},a={any:(0,o.__)("Any"),publish:(0,o.__)("Published"),draft:(0,o.__)("Drafted"),pending:(0,o.__)("Pending"),future:(0,o.__)("Scheduled"),private:(0,o.__)("Private"),trash:(0,o.__)("Trashed")},i={ASC:(0,o.__)("Ascending"),DESC:(0,o.__)("Descending")},p={"":(0,o.__)("List"),thumb:(0,o.__)("Post Thumbnails")};return n().createElement(n().Fragment,null,n().createElement(u.Filter,null,n().createElement(u.Filter.RadioGroupItem,{title:(0,o.__)("Status"),items:a,value:l.post_status,defaultValue:m.post_status,onChange:e=>s({...l,post_status:e})}),n().createElement(u.Filter.LabelsItem,{value:l.label,defaultValue:m.label,onChange:e=>s({...l,label:e})}),n().createElement(u.Filter.RadioGroupItem,{title:(0,o.__)("Display As"),items:p,value:r,defaultValue:d.W.listStyle,onChange:e=>c(e)}),n().createElement(u.Filter.RadioGroupItem,{title:(0,o.__)("Sort By"),items:e,value:l.orderby,defaultValue:m.orderby,onChange:e=>s({...l,orderby:e})}),n().createElement(u.Filter.RadioGroupItem,{title:(0,o.__)("Order"),items:i,value:l.order,defaultValue:d.W.query.order,onChange:e=>s({...l,order:e})}),n().createElement(u.Filter.Button,{onClick:()=>s(m)},(0,o.__)("Reset Filter"))),!t.startsWith("wp_template")&&n().createElement(u.List.InlineCreate,{postType:t,onPostCreated:()=>s({...d.W.query,order:"DESC",orderby:"ID",key:(new Date).getTime()})}))};return n().createElement(u.Layout.Box,{outset:!0,padY:!1,style:{maxHeight:"100%",minHeight:0,flex:"1 1 auto"}},n().createElement(u.List.Posts,{query:{...l,post_type:t},listStyle:r,getItemProps:(e,t)=>e.id?{...t,to:{pathname:`/${a}/post/${e.id}`,state:{item:e}}}:t,before:n().createElement(p,null)}))};(0,c.addFilter)("fl-asst.list-item-actions","fl-assistant",((e,t)=>{let{item:a,listType:l}=t;if("post"===l){const t=e.findIndex((e=>"edit-post"===e.handle));if(t){const l=e[t];delete l.href,l.isShowing=!0,l.title=(0,o.__)("Edit Details"),l.to={pathname:`/fl-content/post/${a.id}`,state:{item:a}}}}return e}));var p=a(7309);const _=e=>{let{baseURL:t}=e;return n().createElement(s.Switch,null,n().createElement(s.Route,{exact:!0,path:t},n().createElement(s.Redirect,{to:{pathname:`${t}/tab/post`}})),n().createElement(s.Route,{path:`${t}/tab/:tab`,component:h}),n().createElement(s.Route,{path:`${t}/post/new`,component:u.Page.CreatePost}),n().createElement(s.Route,{path:`${t}/post/:id`,component:e=>{let{location:t,match:a,history:l}=e;return n().createElement(u.Page.Post,{location:t,match:a,history:l,CloudUI:r})}}))},h=()=>{const{contentTypes:e}=(0,i.getSystemConfig)(),t=()=>n().createElement(u.Layout.Tabs,{tabs:a,shouldHandleOverflow:!0}),a=(()=>{let t=[];return Object.keys(e).map((a=>{const l=e[a];t.push({handle:a,path:"/fl-content/tab/"+a,label:l.labels.plural,component:()=>n().createElement(m,{type:a})})})),t})();return n().createElement(u.Page,{id:"fl-asst-content-list-page",title:(0,o.__)("Content"),icon:n().createElement(p.Z,{context:"sidebar"}),padY:!1,header:n().createElement(t,null),topContentStyle:{border:"none"},shouldScroll:!1,shouldShowBackButton:!1,showAsRoot:!0,onLoad:()=>{const e=document.querySelector(".fl-asst-filter .fluid-button");e&&e.focus()}},n().createElement(u.Layout.CurrentTab,{tabs:a}))}}}]);