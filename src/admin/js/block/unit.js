(function(wp){

    const __ = wp.i18n.__,
        { registerBlockType } = wp.blocks,
        ServerSideRender = wp.serverSideRender,
        { useBlockProps } = wp.blockEditor,
        { Fragment, createElement } = wp.element,
        { SelectControl, CheckboxControl, TextControl, PanelRow, PanelBody } = wp.components,
        { withSelect } = wp.data,
        { compose } = wp.compose,
        { InspectorControls } = wp.editor;
  
    const UnitConfigSelect = compose(withSelect(function(select, selectProps){
      return {posts: select('core').getEntityRecords(
        'postType',
        'helsinki_tpr_unit',
        {
          orderby : 'title',
          order : 'asc',
          per_page: 100,
          status : 'publish',
        }
      )};
    }))(function(props){
  
      var options = [];
      if ( props.posts ) {
        options.push({
          value: 0,
          label: __( 'Select unit', 'helsinki-tpr' )}
        );
  
        props.posts.forEach( function(post) {
          options.push({
            value:post.id,
            label:post.title.rendered
          });
        });
      } else {
        options.push({
          value: 0,
          label: __( 'Loading', 'helsinki-tpr' )}
        );
      }
  
      return createElement(SelectControl, {
        label: __( 'Unit selection', 'helsinki-tpr' ),
        value: props.attributes.postID,
        onChange: function(id) {
          props.setAttributes({
            postID: id,
          });
        },
        options: options,
      });
    });
  
    /**
      * InspectorControls
      */
    function inspectorControls(props) {
      return createElement(
        InspectorControls, {},
        createElement(
          PanelBody, {
            title: __( 'Settings', 'helsinki-tpr' ),
            initialOpen: true,
          },
          configSelectControl(props)
        )
      );
    }

    function informationControls(props) {
        return createElement(
          InspectorControls, {},
          createElement(
            PanelBody, {
              title: __( 'Information', 'helsinki-tpr' ),
              initialOpen: true,
            },
            infoToggleControl({
                label: __( 'Show photo', 'helsinki-tpr' ),
                checked: props.attributes.showPhoto,
                attribute: 'showPhoto',
            }, props),
            infoToggleControl({
                label: __( 'Show street address', 'helsinki-tpr' ),
                checked: props.attributes.showStreetAddress,
                attribute: 'showStreetAddress',
            }, props),
            infoToggleControl({
                label: __( 'Show postal address', 'helsinki-tpr' ),
                checked: props.attributes.showPostalAddress,
                attribute: 'showPostalAddress',
            }, props),
            infoToggleControl({
                label: __( 'Show phone', 'helsinki-tpr' ),
                checked: props.attributes.showPhone,
                attribute: 'showPhone',
            }, props),
            infoToggleControl({
                label: __( 'Show email', 'helsinki-tpr' ),
                checked: props.attributes.showEmail,
                attribute: 'showEmail',
            }, props),
            infoToggleControl({
                label: __( 'Show open hours', 'helsinki-tpr' ),
                checked: props.attributes.showOpenHours,
                attribute: 'showOpenHours',
            }, props),
            infoToggleControl({
                label: __( 'Show website', 'helsinki-tpr' ),
                checked: props.attributes.showWebsite,
                attribute: 'showWebsite',
            }, props),
            infoToggleControl({
                label: __( 'Show additional information', 'helsinki-tpr' ),
                checked: props.attributes.showAdditionalInfo,
                attribute: 'showAdditionalInfo',
            }, props),
          )
        );
      }
  

    function configSelectControl(props) {
      return createElement(
        PanelRow, {},
        createElement(UnitConfigSelect, props)
      );
    }

    function infoToggleControl(config, props) {
        return createElement(
            PanelRow, {},
            createElement(
                CheckboxControl,
                {
                    label: config.label,
                    checked: config.checked,
                    onChange: function(value) {
                        var newAttributes = {};
                        newAttributes[config.attribute] = value;
                        props.setAttributes(newAttributes);
                    }
                }
            )
        );
        }
    
    /**
      * Elements
      */
    function preview(props) {
      return createElement(
        'div', useBlockProps(),
        createElement(ServerSideRender, {
          block: 'helsinki-tpr/unit',
          attributes: props.attributes,
        })
      );
    }
  
    /**
      * Edit
      */
    function edit() {
      return function(props) {
        return createElement(
          Fragment, {},
          inspectorControls(props),
          informationControls(props),
          preview(props)
        );
      }
    }
  
    /**
      * Register
      */
    registerBlockType('helsinki-tpr/unit', {
          apiVersion: 2,
          title: __( 'Helsinki - TPR Unit', 'helsinki-tpr' ),
          category: 'helsinki-tpr',
          icon: 'building',
          keywords: [ __( 'unit', 'helsinki-tpr' ), __( 'tpr', 'helsinki-tpr' ) ],
          supports: {
              html: false,
              anchor: true,
          },
          attributes: {
                postID: {
                    type: 'string',
                    default: 0,
                },
                showStreetAddress: {
                    type: 'boolean',
                    default: true,
                },
                showPostalAddress: {
                    type: 'boolean',
                    default: true,
                },
                showPhone: {
                    type: 'boolean',
                    default: true,
                },
                showEmail: {
                    type: 'boolean',
                    default: true,
                },
                showOpenHours: {
                    type: 'boolean',
                    default: true,
                },
                showWebsite: {
                    type: 'boolean',
                    default: true,
                },
                showAdditionalInfo: {
                    type: 'boolean',
                    default: true,
                },
                showPhoto: {
                    type: 'boolean',
                    default: true,
                },
          },
          edit: edit(),
      });
  
  })(window.wp);
  