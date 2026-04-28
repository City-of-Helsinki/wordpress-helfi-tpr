(function(config){

    'use strict';
  
    const {container} = config;
  
    var tabs, dataContainers;

    if ( container.length > 0 ) {
      for (var i = 0; i < container.length; i++) {
        _initContainerElement( container[i] );
      }
    }
  
    function _initContainerElement( element ) {
        tabs = element.querySelectorAll('.nav-tab');
        var currentTab = element.querySelector('.nav-tab-active');
        dataContainers = element.querySelectorAll('.post-tpr-data');
        var currentDataContainer = element.querySelector('.post-tpr-data.active');
    
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].addEventListener('click', function(event) {
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
    container: document.querySelectorAll('#helsinki-tpr-unit'),
  });
