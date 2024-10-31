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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/components.js":
/*!***************************!*\
  !*** ./src/components.js ***!
  \***************************/
/*! exports provided: SubjectSubTypeAndSubjectSelect, SubjectSubTypeSelect */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SubjectSubTypeAndSubjectSelect", function() { return SubjectSubTypeAndSubjectSelect; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SubjectSubTypeSelect", function() { return SubjectSubTypeSelect; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__);






/**
 *
 */

const SubjectSubTypeAndSubjectSelect = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])((select, props) => {
  if (props.attributes.subjectType === 'post') {
    let postTypes = select('core').getPostTypes({
      per_page: -1
    });
    let postTypeOptions = [];

    if (postTypes) {
      for (let i = 0; i < postTypes.length; i++) {
        postTypeOptions.push({
          "value": postTypes[i].slug,
          "label": postTypes[i].labels.singular_name
        });
      }
    }

    let posts = select('core').getEntityRecords('postType', props.attributes.subjectSubType, {
      search: props.attributes.subjectSearch
    });
    let postOptions = [];

    if (posts) {
      for (let i = 0; i < posts.length; i++) {
        postOptions.push({
          "value": posts[i].id.toString(),
          "label": posts[i].title.raw
        });
      }
    }

    return {
      subjectSubTypeOptions: postTypeOptions,
      subjectOptions: postOptions
    };
  } else if (props.attributes.subjectType === 'user') {
    let users = wp.data.select('core').getUsers({
      search: props.attributes.subjectSearch
    });
    let userOptions = [];

    if (users) {
      for (let i = 0; i < users.length; i++) {
        userOptions.push({
          "value": users[i].id.toString(),
          "label": users[i].name
        });
      }
    }

    return {
      subjectOptions: userOptions
    };
  } else if (props.attributes.subjectType === 'taxonomy') {
    let taxonomies = wp.data.select('core').getTaxonomies({
      per_page: -1
    });
    let taxonomyOptions = [];

    if (taxonomies) {
      for (let i = 0; i < taxonomies.length; i++) {
        taxonomyOptions.push({
          "value": taxonomies[i].slug,
          "label": taxonomies[i].labels.singular_name
        });
      }
    }

    let taxonomyTerms = wp.data.select('core').getEntityRecords('taxonomy', props.attributes.subjectSubType, {
      search: props.attributes.subjectSearch
    });
    let taxonomyTermOptions = [];

    if (taxonomyTerms) {
      for (let i = 0; i < taxonomyTerms.length; i++) {
        taxonomyTermOptions.push({
          "value": taxonomyTerms[i].id.toString(),
          "label": taxonomyTerms[i].name
        });
      }
    }

    return {
      subjectSubTypeOptions: taxonomyOptions,
      subjectOptions: taxonomyTermOptions
    };
  }
})(props => {
  let subjectSubTypeSelect;
  let subjectSelect;

  if (props.attributes.subjectType !== 'user') {
    if (!props.subjectSubTypeOptions) {
      // still resolving
      subjectSubTypeSelect = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Sub Type', 'ratingwp'),
        labelPosition: "top",
        options: [{
          'value': '',
          'label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Loading...', 'ratingwp')
        }]
      });
    } else {
      if (props.attributes.subjectSubType == '' && props.subjectSubTypeOptions && props.subjectSubTypeOptions.length > 0) {
        props.setAttributes({
          subjectSubType: props.subjectSubTypeOptions[0].value
        });
      }

      subjectSubTypeSelect = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["SelectControl"], {
        value: props.attributes.subjectSubType,
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])("Sub Type", "ratingwp"),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            subjectSubType: value,
            subjectId: '',
            subjectSearch: ''
          });
        },
        options: props.subjectSubTypeOptions
      });
    }
  }

  if (!props.subjectOptions) {
    // still resolving
    subjectSelect = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ComboboxControl"], {
      label: "Subject",
      labelPosition: "top",
      onFilterValueChange: () => {},
      options: [{
        "value": "",
        "label": Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Loading...', 'ratingwp')
      }]
    });
  } else {
    subjectSelect = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["ComboboxControl"], {
      label: "Subject",
      labelPosition: "top",
      value: props.attributes.subjectId,
      onFilterValueChange: value => {
        props.setAttributes({
          subjectSearch: value
        });
      },
      options: props.subjectOptions,
      onInputChange: value => setFilteredOptions({
        options
      }.filter(option => option.label.toLowerCase().startsWith(value.toLowerCase()))),
      onChange: value => {
        props.setAttributes({
          subjectId: value
        });
      }
    });
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, props.attributes.subjectType != 'user' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelRow"], null, subjectSubTypeSelect), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelRow"], null, subjectSelect));
});
/**
 * 
 */

const SubjectSubTypeSelect = Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__["withSelect"])((select, props) => {
  if (props.attributes.subjectType === 'post') {
    let postTypes = select('core').getPostTypes({
      per_page: -1
    });
    let postTypeOptions = [];

    if (postTypes) {
      for (let i = 0; i < postTypes.length; i++) {
        postTypeOptions.push({
          "value": postTypes[i].slug,
          "label": postTypes[i].labels.singular_name
        });
      }
    }

    return {
      subjectSubTypeOptions: postTypeOptions
    };
  } else if (props.attributes.subjectType === 'taxonomy') {
    let taxonomies = wp.data.select('core').getTaxonomies({
      per_page: -1
    });
    let taxonomyOptions = [];

    if (taxonomies) {
      for (let i = 0; i < taxonomies.length; i++) {
        taxonomyOptions.push({
          "value": taxonomies[i].slug,
          "label": taxonomies[i].labels.singular_name
        });
      }
    }

    return {
      subjectSubTypeOptions: taxonomyOptions
    };
  }
})(props => {
  let subjectSubTypeSelect;

  if (!props.subjectSubTypeOptions) {
    // still resolving
    subjectSubTypeSelect = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["SelectControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Sub Type', 'ratingwp'),
      labelPosition: "top",
      options: [{
        'value': '',
        'label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])('Loading...', 'ratingwp')
      }]
    });
  } else {
    if (props.attributes.subjectSubType == '' && props.subjectSubTypeOptions && props.subjectSubTypeOptions.length > 0) {
      props.setAttributes({
        subjectSubType: props.subjectSubTypeOptions[0].value
      });
    }

    subjectSubTypeSelect = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["SelectControl"], {
      value: props.attributes.subjectSubType,
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__["__"])("Sub Type", "ratingwp"),
      labelPosition: "top",
      onChange: value => {
        props.setAttributes({
          subjectSubType: value
        });
      },
      options: props.subjectSubTypeOptions
    });
  }

  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_4__["PanelRow"], null, subjectSubTypeSelect));
});

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./store */ "./src/store.js");
/* harmony import */ var _components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components */ "./src/components.js");



Object(_store__WEBPACK_IMPORTED_MODULE_1__["default"])();

(function () {
  // local scope
  const {
    registerBlockType
  } = wp.blocks; // Blocks API

  const {
    __
  } = wp.i18n; // translation functions

  const {
    InspectorControls
  } = wp.blockEditor;
  const {
    PanelBody,
    PanelRow,
    Panel,
    TextControl,
    SelectControl,
    Button,
    Notice,
    ToggleControl,
    ColorPicker,
    RadioControl,
    BaseControl
  } = wp.components;
  const {
    compose
  } = wp.compose;
  const {
    serverSideRender: ServerSideRender
  } = wp;
  const {
    useSelect
  } = wp.data;
  /*
   * Rating form block
   */

  registerBlockType('ratingwp/rating-form', {
    // Built-in attributes
    title: __('Rating Form', 'ratingwp'),
    description: __('Adds a rating form.', 'ratingwp'),
    icon: 'star-filled',
    category: 'common',
    keywords: [__('review', 'ratingwp'), __('rating', 'ratingwp')],
    // Built-in functions
    edit: function (props) {
      let forms = useSelect(select => select('ratingwp').getForms(), []);
      let formOptions = [];

      for (let i = 0; i < forms.length; i++) {
        formOptions.push({
          "value": forms[i].id,
          "label": forms[i].name
        });
      } // init post type


      if (props.attributes.useCurrentPostAsSubject && props.attributes.subjectSubType === undefined) {
        props.setAttributes({
          subjectType: 'post',
          subjectSubType: wp.data.select('core/editor').getCurrentPostType()
        });
      }

      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ServerSideRender, {
        block: "ratingwp/rating-form",
        attributes: props.attributes
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Panel, {
        className: "rating-form-block-settings"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Form settings', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.formId,
        label: __("Form", "ratingwp"),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            formId: parseInt(value)
          });
        },
        options: formOptions
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Subject details', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ToggleControl, {
        label: __('Use current post as subject', 'ratingwp'),
        checked: props.attributes.useCurrentPostAsSubject,
        onChange: value => {
          props.setAttributes({
            useCurrentPostAsSubject: value,
            subjectType: 'post',
            subjectSubType: wp.data.select('core/editor').getCurrentPostType(),
            subjectId: '',
            subjectSearch: ''
          });
        }
      })), !props.attributes.useCurrentPostAsSubject && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.subjectType,
        label: __('Type', 'ratingwp'),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            subjectType: value,
            subjectSubType: '',
            subjectId: '',
            subjectSearch: ''
          });
        },
        className: props.className,
        options: [{
          value: 'post',
          label: __('Post', 'ratingwp')
        }, {
          value: 'user',
          label: __('User', 'ratingwp')
        }, {
          value: 'taxonomy',
          label: __('Taxonomy', 'ratingwp')
        }]
      })), !props.attributes.useCurrentPostAsSubject && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components__WEBPACK_IMPORTED_MODULE_2__["SubjectSubTypeAndSubjectSelect"], props)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Styles', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BaseControl, {
        label: __('Primary Color', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ColorPicker, {
        color: props.attributes.primaryColor,
        onChangeComplete: value => {
          props.setAttributes({
            primaryColor: value.hex
          });
        }
      })))))));
    },

    /**
     * The save function returns null as this is a dynamic block. The block is rendered 
     * server side instead.
     */
    save: function (props) {
      return null;
    }
  });
  /*
   * Rating summary block
   */

  registerBlockType('ratingwp/rating-summary', {
    // Built-in attributes
    title: __('Rating Summary', 'ratingwp'),
    description: __('Adds a rating summary.', 'ratingwp'),
    icon: 'star-filled',
    category: 'common',
    keywords: [__('review', 'ratingwp'), __('rating', 'ratingwp')],
    // Built-in functions
    edit: function (props) {
      let forms = useSelect(select => select('ratingwp').getForms(), []);
      let formOptions = [];

      for (let i = 0; i < forms.length; i++) {
        formOptions.push({
          "value": forms[i].id,
          "label": forms[i].name
        });
      } // init post type


      if (props.attributes.useCurrentPostAsSubject && props.attributes.subjectSubType === undefined) {
        props.setAttributes({
          subjectType: 'post',
          subjectSubType: wp.data.select('core/editor').getCurrentPostType()
        });
      }

      let showHeader = props.attributes.layout === 'overall';
      let showPrimaryColor = props.attributes.layout === 'details' || props.attributes.resultType === 'star-rating';
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ServerSideRender, {
        block: "ratingwp/rating-summary",
        attributes: props.attributes
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Panel, {
        className: "rating-summary-block-settings"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Form settings', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.formId,
        label: __("Form", "ratingwp"),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            formId: parseInt(value)
          });
        },
        options: formOptions
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Subject details', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ToggleControl, {
        label: __('Use current post as subject', 'ratingwp'),
        checked: props.attributes.useCurrentPostAsSubject,
        onChange: value => {
          props.setAttributes({
            useCurrentPostAsSubject: value,
            subjectType: 'post',
            subjectSubType: wp.data.select('core/editor').getCurrentPostType(),
            subjectId: '',
            subjectSearch: ''
          });
        }
      })), !props.attributes.useCurrentPostAsSubject && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.subjectType,
        label: __('Type', 'ratingwp'),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            subjectType: value,
            subjectSubType: '',
            subjectId: '',
            subjectSearch: ''
          });
        },
        className: props.className,
        options: [{
          value: 'post',
          label: __('Post', 'ratingwp')
        }, {
          value: 'user',
          label: __('User', 'ratingwp')
        }, {
          value: 'taxonomy',
          label: __('Taxonomy', 'ratingwp')
        }]
      })), !props.attributes.useCurrentPostAsSubject && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components__WEBPACK_IMPORTED_MODULE_2__["SubjectSubTypeAndSubjectSelect"], props)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Styles', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(RadioControl, {
        label: __('Layout', 'ratingwp'),
        selected: props.attributes.layout,
        options: [{
          label: __('Overall', 'ratingwp'),
          value: 'overall'
        }, {
          label: __('Details', 'ratingwp'),
          value: 'details'
        }],
        onChange: value => {
          props.setAttributes({
            layout: value
          });
        }
      })), props.attributes.layout == 'overall' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(RadioControl, {
        label: __('Text Align', 'ratingwp'),
        selected: props.attributes.textAlign,
        options: [{
          label: __('Left', 'ratingwp'),
          value: 'left'
        }, {
          label: __('Center', 'ratingwp'),
          value: 'center'
        }, {
          label: __('Right', 'ratingwp'),
          value: 'right'
        }],
        onChange: value => {
          props.setAttributes({
            textAlign: value
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(RadioControl, {
        label: __('Result Type', 'ratingwp'),
        selected: props.attributes.resultType,
        options: [{
          label: __('Score', 'ratingwp'),
          value: 'score'
        }, {
          label: __('Star Rating', 'ratingwp'),
          value: 'star-rating'
        }, {
          label: __('Percentage', 'ratingwp'),
          value: 'percentage'
        }],
        onChange: value => {
          props.setAttributes({
            resultType: value
          });
        }
      })), showHeader && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.header,
        label: __('Header', 'ratingwp'),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            header: value
          });
        },
        options: [{
          value: 'h1',
          label: __('H1', 'ratingwp')
        }, {
          value: 'h2',
          label: __('H2', 'ratingwp')
        }, {
          value: 'h3',
          label: __('H3', 'ratingwp')
        }]
      })), showPrimaryColor && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BaseControl, {
        label: __('Primary Color', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ColorPicker, {
        color: props.attributes.primaryColor,
        onChangeComplete: value => {
          props.setAttributes({
            primaryColor: value.hex
          });
        }
      })))))));
    }
  });
  /*
   * Rating list table block
   *
   */

  registerBlockType('ratingwp/rating-list-table', {
    // Built-in attributes
    title: __('Rating List Table', 'ratingwp'),
    description: __('Adds a list table of subject ratings.', 'ratingwp'),
    icon: 'star-filled',
    category: 'common',
    keywords: [__('review', 'ratingwp'), __('rating', 'ratingwp')],
    // Built-in functions
    edit: function (props) {
      let forms = useSelect(select => select('ratingwp').getForms(), []);
      let formOptions = [];

      for (let i = 0; i < forms.length; i++) {
        formOptions.push({
          "value": forms[i].id,
          "label": forms[i].name
        });
      }

      let showPrimaryColor = props.attributes.resultType === 'star-rating';
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ServerSideRender, {
        block: "ratingwp/rating-list-table",
        attributes: props.attributes
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Panel, {
        className: "rating-list-table-block-settings"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Form settings', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.formId,
        label: __("Form", "ratingwp"),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            formId: parseInt(value)
          });
        },
        options: formOptions
      }))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Subject details', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.subjectType,
        label: __('Type', 'ratingwp'),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            subjectType: value,
            subjectSubType: ''
          });
        },
        options: [{
          value: 'post',
          label: __('Post', 'ratingwp')
        }, {
          value: 'user',
          label: __('User', 'ratingwp')
        }, {
          value: 'taxonomy',
          label: __('Taxonomy', 'ratingwp')
        }]
      })), props.attributes.subjectType !== 'user' && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_components__WEBPACK_IMPORTED_MODULE_2__["SubjectSubTypeSelect"], props)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Styles', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(SelectControl, {
        value: props.attributes.defaultStyle,
        label: __('Default Style', 'ratingwp'),
        labelPosition: "top",
        onChange: value => {
          props.setAttributes({
            defaultStyle: value
          });
        },
        options: [{
          value: null,
          label: __('Not set', 'ratingwp')
        }, {
          value: 'default',
          label: __('Default', 'ratingwp')
        }, {
          value: 'stripes',
          label: __('Stripes', 'ratingwp')
        }]
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(RadioControl, {
        label: __('Layout', 'ratingwp'),
        selected: props.attributes.layout,
        options: [{
          label: __('Table', 'ratingwp'),
          value: 'table'
        }],
        onChange: value => {
          props.setAttributes({
            layout: value
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(RadioControl, {
        label: __('Result Type', 'ratingwp'),
        selected: props.attributes.resultType,
        options: [{
          label: __('Score', 'ratingwp'),
          value: 'score'
        }, {
          label: __('Star Rating', 'ratingwp'),
          value: 'star-rating'
        }, {
          label: __('Percentage', 'ratingwp'),
          value: 'percentage'
        }],
        onChange: value => {
          props.setAttributes({
            resultType: value
          });
        }
      })), showPrimaryColor && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(BaseControl, {
        label: __('Primary Color', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ColorPicker, {
        color: props.attributes.primaryColor,
        onChangeComplete: value => {
          props.setAttributes({
            primaryColor: value.hex
          });
        }
      })))), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelBody, {
        title: __('Table settings', 'ratingwp')
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ToggleControl, {
        label: __('Fixed width table cells', 'ratingwp'),
        checked: props.attributes.fixedWidthTableCells,
        onChange: value => {
          props.setAttributes({
            fixedWidthTableCells: value
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ToggleControl, {
        label: __('Show Header', 'ratingwp'),
        checked: props.attributes.showHeader,
        onChange: value => {
          props.setAttributes({
            showHeader: value
          });
        }
      })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(PanelRow, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(ToggleControl, {
        label: __('Show Rank', 'ratingwp'),
        checked: props.attributes.showRank,
        onChange: value => {
          props.setAttributes({
            showRank: value
          });
        }
      }))))));
    },

    /**
     * The save function returns null as this is a dynamic block. The block is rendered 
     * server side instead.
     */
    save: function (props) {
      return null;
    }
  });
})();

/***/ }),

/***/ "./src/store.js":
/*!**********************!*\
  !*** ./src/store.js ***!
  \**********************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress data store for RatingWP plugin
 */

/* harmony default export */ __webpack_exports__["default"] = (() => {
  const RAWP_DEFAULT_STATE = {
    forms: []
  };
  const actions = {
    setForms(forms) {
      return {
        type: 'SET_FORMS',
        forms
      };
    },

    getForms(path) {
      return {
        type: 'GET_FORMS',
        path
      };
    }

  };

  const reducer = (state = RAWP_DEFAULT_STATE, action) => {
    switch (action.type) {
      case 'SET_FORMS':
        {
          return {
            state,
            forms: action.forms
          };
          break;
        }

      default:
        {
          return state;
        }
    }
  };

  const selectors = {
    getForms(state) {
      const {
        forms
      } = state;
      return forms;
    }

  };
  const controls = {
    GET_FORMS(action) {
      return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_1___default()({
        path: action.path
      });
    }

  };
  const resolvers = {
    *getForms() {
      const forms = yield actions.getForms('/ratingwp/v1/forms/');
      return actions.setForms(forms);
    }

  };
  const storeConfig = {
    reducer,
    controls,
    selectors,
    resolvers,
    actions
  };
  Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__["registerStore"])('ratingwp', storeConfig);
});

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map