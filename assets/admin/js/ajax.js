(function(config){

    'use strict';
  
    const {importButtons, ajax} = config;
    
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

      _ajaxRequest( ajax.ajaxUrl, _formData )
        .then(function(response){
          if ( response ) {
            response = JSON.parse(response);
            console.log(response.data.link_html);
          }
          spinner.style.visibility = 'hidden';
          button.parentElement.innerHTML = response.data.link_html;
        })
        .catch(function(error){
          spinner.style.visibility = 'hidden';
          button.disabled = false;
        });

    }

    function _ajaxRequest( endpoint, formData ) {
      return new Promise(function(resolve, reject){
        var xhr = new XMLHttpRequest();
            xhr.open( 'POST', endpoint );
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
            xhr.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
            xhr.send( formData );
      });
    }
  
  })({
    importButtons: document.querySelectorAll('.helsinki-tpr-import-button'),
    ajax: helsinkiTPR,
  });
  