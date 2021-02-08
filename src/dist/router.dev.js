"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _vue = _interopRequireDefault(require("vue"));

var _vueRouter = _interopRequireDefault(require("vue-router"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _getRequireWildcardCache() { if (typeof WeakMap !== "function") return null; var cache = new WeakMap(); _getRequireWildcardCache = function _getRequireWildcardCache() { return cache; }; return cache; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

_vue["default"].use(_vueRouter["default"]);

var _default = new _vueRouter["default"]({
  mode: 'hash',
  base: process.env.BASE_URL,
  routes: [{
    path: '/',
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('@/views/dashboard/Index'));
      });
    },
    children: [// Dashboard
    {
      name: 'Dashboard',
      path: '',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/Dashboard'));
        });
      }
    }, // Pages
    {
      name: 'Customer Profile',
      path: 'pages/customer/:id',
      props: true,
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/pages/CustomerProfile'));
        });
      }
    }, {
      name: 'Paynments',
      path: 'tables/payments/:customerid',
      props: true,
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/tables/Payments'));
        });
      }
    }, {
      name: 'Notifications',
      path: 'components/notifications',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/component/Notifications'));
        });
      }
    }, {
      name: 'Icons',
      path: 'components/icons',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/component/Icons'));
        });
      }
    }, {
      name: 'Typography',
      path: 'components/typography',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/component/Typography'));
        });
      }
    }, // Tables
    {
      name: 'Customers',
      path: 'tables/customers',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/tables/Customers'));
        });
      }
    }, // {
    //   name: 'Invoices',
    //   path: 'tables/invoiceslist',
    //   component: () => import('@/views/dashboard/tables/InvoicesList'),
    // },
    {
      name: 'Invoices',
      path: 'tables/invoices/:invoiceid',
      props: true,
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/tables/Invoices'));
        });
      }
    }, {
      name: 'Terminals',
      path: 'tables/terminals',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/tables/Terminals'));
        });
      }
    }, // Maps
    {
      name: 'Google Maps',
      path: 'maps/google-maps',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/maps/GoogleMaps'));
        });
      }
    }, // Upgrade
    {
      name: 'Upgrade',
      path: 'upgrade',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/Upgrade'));
        });
      }
    }, {
      name: 'Tabs',
      path: '/component/tabs',
      component: function component() {
        return Promise.resolve().then(function () {
          return _interopRequireWildcard(require('@/views/dashboard/component/Notifications'));
        });
      }
    }]
  }]
});

exports["default"] = _default;