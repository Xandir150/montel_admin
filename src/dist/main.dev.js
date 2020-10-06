"use strict";

var _vue = _interopRequireDefault(require("vue"));

var _App = _interopRequireDefault(require("./App.vue"));

var _router = _interopRequireDefault(require("./router"));

var _store = _interopRequireDefault(require("./store"));

require("./plugins/base");

require("./plugins/chartist");

require("./plugins/vee-validate");

var _vuetify = _interopRequireDefault(require("./plugins/vuetify"));

var _i18n = _interopRequireDefault(require("./i18n"));

var _upperFirst = _interopRequireDefault(require("lodash/upperFirst"));

var _camelCase = _interopRequireDefault(require("lodash/camelCase"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

_vue["default"].config.productionTip = false;

var requireComponent = require.context( // Относительный путь до каталога компонентов
'./components', // Обрабатывать или нет подкаталоги
false, // Регулярное выражение для определения файлов базовых компонентов
/[A-Z]\w+\.(vue|js)$/);

requireComponent.keys().forEach(function (fileName) {
  // Получение конфигурации компонента
  var componentConfig = requireComponent(fileName); // Получение имени компонента в PascalCase

  var componentName = (0, _upperFirst["default"])((0, _camelCase["default"])(fileName.replace(/^\.\//, '').replace(/\.\w+$/, ''))); // Глобальная регистрация компонента

  _vue["default"].component(componentName, // Поиск опций компонента в `.default`, который будет существовать,
  // если компонент экспортирован с помощью `export default`,
  // иначе будет использован корневой уровень модуля.
  componentConfig["default"] || componentConfig);
});

_vue["default"].mixin({
  methods: {
    getTimeOffset: function getTimeOffset() {
      return new Date().getTimezoneOffset();
    }
  }
});

new _vue["default"]({
  router: _router["default"],
  store: _store["default"],
  vuetify: _vuetify["default"],
  i18n: _i18n["default"],
  render: function render(h) {
    return h(_App["default"]);
  }
}).$mount('#app');