(function(wp){

    const __ = wp.i18n.__;
    const { unregisterBlockType, unregisterBlockVariation, getBlockType, getBlockVariations } = wp.blocks;

    wp.domReady( function() {
        if (document.querySelector('body').classList.contains('post-type-post')) {
            if (getBlockType('helsinki-tpr/unit')) {
                unregisterBlockType( 'helsinki-tpr/unit' );
            }    
        }
    });

})(window.wp);
