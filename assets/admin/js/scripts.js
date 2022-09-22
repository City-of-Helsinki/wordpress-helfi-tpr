"use strict";

(function (config) {
  'use strict';

  var importButtons = config.importButtons,
      ajax = config.ajax;
  console.log(importButtons);

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

    console.log(button.getAttribute('data-tpr-id'));

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
  search: document.querySelectorAll('.helsinki-tpr-search'),
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