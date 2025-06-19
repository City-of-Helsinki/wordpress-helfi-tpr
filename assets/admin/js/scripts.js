"use strict";

(function (config) {
  'use strict';

  var importButtons = config.importButtons,
      ajax = config.ajax;

  if (importButtons.length > 0) {
    for (var i = 0; i < importButtons.length; i++) {
      importButtons[i].addEventListener('click', _clickImport);
    }
  }

  function _clickImport(event) {
    event.preventDefault();

    _importUnit(event.currentTarget);
  }

  function _importUnit(button) {
    button.disabled = true;
    var spinner = button.nextElementSibling;
    spinner.style.visibility = 'visible';

    var _formData = new FormData();

    _formData.append('action', 'helsinki_import_tpr_unit');

    _formData.append('id', button.getAttribute('data-tpr-id'));

    _formData.append('title', button.getAttribute('data-tpr-title'));

    _ajaxRequest(ajax.ajaxUrl, _formData).then(function (response) {
      if (response) {
        response = JSON.parse(response);
        console.log(response.data.link_html);
      }

      spinner.style.visibility = 'hidden';
      button.parentElement.innerHTML = response.data.link_html;
    }).catch(function (error) {
      spinner.style.visibility = 'hidden';
      button.disabled = false;
    });
  }

  function _ajaxRequest(endpoint, formData) {
    return new Promise(function (resolve, reject) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', endpoint);

      xhr.onload = function () {
        if (this.status >= 200 && this.status < 300) {
          resolve(xhr.response);
        } else {
          reject(xhr.response);
        }
      };

      xhr.onerror = function () {
        reject({
          status: this.status,
          message: xhr.statusText
        });
      };

      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.send(formData);
    });
  }
})({
  importButtons: document.querySelectorAll('.helsinki-tpr-import-button'),
  ajax: helsinkiTPR
});

(function (config) {
  'use strict';

  var container = config.container;
  var tabs, dataContainers;

  if (container.length > 0) {
    for (var i = 0; i < container.length; i++) {
      _initContainerElement(container[i]);
    }
  }

  function _initContainerElement(element) {
    tabs = element.querySelectorAll('.nav-tab');
    var currentTab = element.querySelector('.nav-tab-active');
    dataContainers = element.querySelectorAll('.post-tpr-data');
    var currentDataContainer = element.querySelector('.post-tpr-data.active');

    for (var i = 0; i < tabs.length; i++) {
      tabs[i].addEventListener('click', function (event) {
        var tab = event.currentTarget;
        var dC = element.querySelector('.' + tab.getAttribute('data-container'));

        if (tab != currentTab) {
          currentTab.classList.remove('nav-tab-active');
          currentDataContainer.classList.remove('active');
          tab.classList.add('nav-tab-active');
          dC.classList.add('active');
          currentTab = tab;
          currentDataContainer = dC;
        }
      });
    }
  }
})({
  container: document.querySelectorAll('#helsinki-tpr-unit')
});

(function (wp) {
  var __ = wp.i18n.__;
  var _wp$blocks = wp.blocks,
      unregisterBlockType = _wp$blocks.unregisterBlockType,
      unregisterBlockVariation = _wp$blocks.unregisterBlockVariation,
      getBlockType = _wp$blocks.getBlockType,
      getBlockVariations = _wp$blocks.getBlockVariations;
  wp.domReady(function () {
    if (document.querySelector('body').classList.contains('post-type-post')) {
      if (getBlockType('helsinki-tpr/unit')) {
        unregisterBlockType('helsinki-tpr/unit');
      }
    }
  });
})(window.wp);

(function (wp) {
  var __ = wp.i18n.__,
      registerBlockType = wp.blocks.registerBlockType,
      ServerSideRender = wp.serverSideRender,
      useBlockProps = wp.blockEditor.useBlockProps,
      _wp$element = wp.element,
      Fragment = _wp$element.Fragment,
      createElement = _wp$element.createElement,
      _wp$components = wp.components,
      SelectControl = _wp$components.SelectControl,
      CheckboxControl = _wp$components.CheckboxControl,
      TextControl = _wp$components.TextControl,
      PanelRow = _wp$components.PanelRow,
      PanelBody = _wp$components.PanelBody,
      withSelect = wp.data.withSelect,
      compose = wp.compose.compose,
      InspectorControls = wp.editor.InspectorControls;
  var UnitConfigSelect = compose(withSelect(function (select, selectProps) {
    return {
      posts: select('core').getEntityRecords('postType', 'helsinki_tpr_unit', {
        orderby: 'title',
        order: 'asc',
        per_page: 100,
        status: 'publish'
      })
    };
  }))(function (props) {
    var options = [];

    if (props.posts) {
      options.push({
        value: 0,
        label: __('Select unit', 'helsinki-tpr')
      });
      props.posts.forEach(function (post) {
        options.push({
          value: post.id,
          label: post.title.rendered
        });
      });
    } else {
      options.push({
        value: 0,
        label: __('Loading', 'helsinki-tpr')
      });
    }

    return createElement(SelectControl, {
      label: __('Unit selection', 'helsinki-tpr'),
      value: props.attributes.postID,
      onChange: function onChange(id) {
        props.setAttributes({
          postID: id
        });
      },
      options: options
    });
  });
  /**
    * InspectorControls
    */

  function inspectorControls(props) {
    return createElement(InspectorControls, {}, createElement(PanelBody, {
      title: __('Settings', 'helsinki-tpr'),
      initialOpen: true
    }, configSelectControl(props), unitTitleControl({
      label: __('Title', 'helsinki-tpr'),
      value: props.attributes.unitTitle,
      attribute: 'unitTitle'
    }, props)));
  }

  function informationControls(props) {
    return createElement(InspectorControls, {}, createElement(PanelBody, {
      title: __('Information', 'helsinki-tpr'),
      initialOpen: true
    }, infoToggleControl({
      label: __('Show photo', 'helsinki-tpr'),
      checked: props.attributes.showPhoto,
      attribute: 'showPhoto'
    }, props), infoToggleControl({
      label: __('Show street address', 'helsinki-tpr'),
      checked: props.attributes.showStreetAddress,
      attribute: 'showStreetAddress'
    }, props), infoToggleControl({
      label: __('Show email', 'helsinki-tpr'),
      checked: props.attributes.showEmail,
      attribute: 'showEmail'
    }, props), infoToggleControl({
      label: __('Show phone', 'helsinki-tpr'),
      checked: props.attributes.showPhone,
      attribute: 'showPhone'
    }, props), infoToggleControl({
      label: __('Show open hours', 'helsinki-tpr'),
      checked: props.attributes.showOpenHours,
      attribute: 'showOpenHours'
    }, props), infoToggleControl({
      label: __('Show service language', 'helsinki-tpr'),
      checked: props.attributes.showServiceLanguage,
      attribute: 'showServiceLanguage'
    }, props), infoToggleControl({
      label: __('Show website', 'helsinki-tpr'),
      checked: props.attributes.showWebsite,
      attribute: 'showWebsite'
    }, props), infoToggleControl({
      label: __('Show postal address', 'helsinki-tpr'),
      checked: props.attributes.showPostalAddress,
      attribute: 'showPostalAddress'
    }, props), infoToggleControl({
      label: __('Show directions', 'helsinki-tpr'),
      checked: props.attributes.showDirections,
      attribute: 'showDirections'
    }, props), infoToggleControl({
      label: __('Show additional information', 'helsinki-tpr'),
      checked: props.attributes.showAdditionalInfo,
      attribute: 'showAdditionalInfo'
    }, props)));
  }

  function configSelectControl(props) {
    return createElement(PanelRow, {}, createElement(UnitConfigSelect, props));
  }

  function infoToggleControl(config, props) {
    return createElement(PanelRow, {}, createElement(CheckboxControl, {
      label: config.label,
      checked: config.checked,
      onChange: function onChange(value) {
        var newAttributes = {};
        newAttributes[config.attribute] = value;
        props.setAttributes(newAttributes);
      }
    }));
  }

  function unitTitleControl(config, props) {
    return createElement(PanelRow, {}, createElement(TextControl, {
      label: config.label,
      value: config.value,
      onChange: function onChange(value) {
        var newAttributes = {};
        newAttributes[config.attribute] = value;
        props.setAttributes(newAttributes);
      }
    }));
  }
  /**
    * Elements
    */


  function preview(props) {
    return createElement('div', useBlockProps(), createElement(ServerSideRender, {
      block: 'helsinki-tpr/unit',
      attributes: props.attributes
    }));
  }
  /**
    * Edit
    */


  function edit() {
    return function (props) {
      return createElement(Fragment, {}, inspectorControls(props), informationControls(props), preview(props));
    };
  }
  /**
    * Register
    */


  registerBlockType('helsinki-tpr/unit', {
    apiVersion: 2,
    title: __('Helsinki - Unit (TPR)', 'helsinki-tpr'),
    category: 'helsinki-tpr',
    icon: 'building',
    keywords: [__('unit', 'helsinki-tpr'), __('tpr', 'helsinki-tpr'), __('Helsinki - TPR Unit', 'helsinki-tpr')],
    supports: {
      html: false,
      anchor: true
    },
    attributes: {
      postID: {
        type: 'string',
        default: 0
      },
      unitTitle: {
        type: 'string',
        default: ''
      },
      showStreetAddress: {
        type: 'boolean',
        default: true
      },
      showPostalAddress: {
        type: 'boolean',
        default: true
      },
      showPhone: {
        type: 'boolean',
        default: true
      },
      showEmail: {
        type: 'boolean',
        default: true
      },
      showDirections: {
        type: 'boolean',
        default: true
      },
      showServiceLanguage: {
        type: 'boolean',
        default: true
      },
      showOpenHours: {
        type: 'boolean',
        default: true
      },
      showWebsite: {
        type: 'boolean',
        default: true
      },
      showAdditionalInfo: {
        type: 'boolean',
        default: true
      },
      showPhoto: {
        type: 'boolean',
        default: true
      },
      anchor: {
        type: 'string',
        default: ''
      }
    },
    edit: edit()
  });
})(window.wp);