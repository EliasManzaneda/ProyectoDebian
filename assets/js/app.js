var $ = require('jquery');

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);

require('bootstrap-sass');
require('../css/app.scss');
require('../css/user_login.scss');
