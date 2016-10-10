The plugin basically hooks up to 'login_init', and listens to requests which have a Response-Type header set to 'json'. It also checks for `$_REQUEST['RESPONSE_TYPE']`, if for some reason you can't set the request headers.

# Installing
The best way to install is using Composer.

`composer require redandblue/rnb-ajax-login`

Traditional methods work too.

# Example

Code sample (ES6+, production usage requires you to use a Webpack or Rollup and Babel or Bublé):

```html
<form action="/wp-login.php">
  <!-- The input names are critical! -->
  <input type="text" name="log">
  <input type="password" name="pwd">
  <input type="checkbox" name="rememberme" value="forever">

  <!-- If you'd rather do it with an input. I'd recommend using the headers. -->
  <input type="hidden" name="RESPONSE_TYPE" value="json">
  <!-- Also note that this will kill the nojs-fallback. -->

  <input type="submit">
</form>
```

```javascript
// ajax-login.js

import 'whatwg-fetch';

export default function(){
  const ajaxLogin = e => {
    const form = e.target;
    const data = new FormData(form);
    const headers = new Headers({
      'Response-Type': 'json'
    });

    fetch(form.action, {
      method: 'POST',
      body: data,
      headers: headers,
      credentials: 'include'
    })
    .then(response => response.json())
    .then(response => {
      if(response.type === 'success'){
        console.log(response.message);
      } else {
        console.error(response.message);
      }
    });

    e.preventDefault();
  };

  [...document.querySelectorAll('[action="/wp-login.php"]')].forEach(form => {
    form[0].addEventListener('submit', ajaxLogin);
  });
}

```

```javascript
// main.js
import ajaxLogin from './ajax-login.js';
ajaxLogin();
```

This allows you to AJAXify any standard WP login form. If the user has JavaScript disabled, it will gracefully fallback to the vanilla WordPress way.
