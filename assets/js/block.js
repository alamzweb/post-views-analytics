(function(blocks, element, blockEditor) {
    var el = element.createElement;

    blocks.registerBlockType('post-views-analytics/display-block', {
        title: 'Post Views Metrics Display',
        icon: 'chart-bar',
        category: 'widgets',
        edit: function() {
            return el(
                'div',
                { style: { padding: '15px', background: '#f8f9fa', border: '2px dashed #c3c4c7', borderRadius: '8px', textAlign: 'center', color: '#50575e' } },
                '📊 [Post Views Analytics Badge Place-holder]'
            );
        },
        save: function() {
            return null; // Render dynamically via PHP backend routing callbacks
        },
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor);