/**
 * Dark Mode Toggle Shortcode Block for Gutenberg
 */
(function(blocks, element, blockEditor, components) {
    const { registerBlockType } = blocks;
    const { createElement } = element;
    const { InspectorControls } = blockEditor;
    const { PanelBody, SelectControl, ToggleControl } = components;
    
    registerBlockType('ote/dark-mode-shortcode', {
        title: 'Dark Mode Toggle',
        description: 'Add a dark mode toggle using shortcode',
        category: 'widgets',
        icon: 'admin-appearance',
        keywords: ['dark', 'mode', 'toggle', 'theme'],
        
        attributes: {
            style: {
                type: 'string',
                default: 'button'
            },
            size: {
                type: 'string',
                default: 'medium'
            },
            showLabel: {
                type: 'boolean',
                default: true
            },
            align: {
                type: 'string',
                default: 'left'
            }
        },
        
        edit: function(props) {
            const { attributes, setAttributes } = props;
            
            return createElement('div', {}, [
                createElement(InspectorControls, { key: 'inspector' }, 
                    createElement(PanelBody, { title: 'Dark Mode Toggle Settings', initialOpen: true }, [
                        createElement(SelectControl, {
                            key: 'style',
                            label: 'Style',
                            value: attributes.style,
                            options: [
                                { label: 'Button', value: 'button' },
                                { label: 'Icon Only', value: 'icon' },
                                { label: 'Pill', value: 'pill' }
                            ],
                            onChange: function(value) { setAttributes({ style: value }); }
                        }),
                        createElement(SelectControl, {
                            key: 'size',
                            label: 'Size',
                            value: attributes.size,
                            options: [
                                { label: 'Small', value: 'small' },
                                { label: 'Medium', value: 'medium' },
                                { label: 'Large', value: 'large' }
                            ],
                            onChange: function(value) { setAttributes({ size: value }); }
                        }),
                        createElement(ToggleControl, {
                            key: 'showLabel',
                            label: 'Show Label',
                            checked: attributes.showLabel,
                            onChange: function(value) { setAttributes({ showLabel: value }); }
                        }),
                        createElement(SelectControl, {
                            key: 'align',
                            label: 'Alignment',
                            value: attributes.align,
                            options: [
                                { label: 'Left', value: 'left' },
                                { label: 'Center', value: 'center' },
                                { label: 'Right', value: 'right' }
                            ],
                            onChange: function(value) { setAttributes({ align: value }); }
                        })
                    ])
                ),
                createElement('div', {
                    key: 'preview',
                    className: 'ote-shortcode-preview',
                    style: {
                        border: '2px dashed #e2e8f0',
                        borderRadius: '10px',
                        padding: '20px',
                        textAlign: attributes.align,
                        background: '#f8fafc'
                    }
                }, [
                    createElement('div', {
                        key: 'header',
                        style: {
                            marginBottom: '16px',
                            paddingBottom: '10px',
                            borderBottom: '1px solid #e2e8f0'
                        }
                    }, [
                        createElement('h4', {
                            key: 'title',
                            style: { margin: '0 0 8px 0', color: '#1e293b' }
                        }, 'Dark Mode Toggle'),
                        createElement('div', {
                            key: 'shortcode',
                            style: {
                                fontSize: '12px',
                                fontFamily: 'monospace',
                                color: '#64748b',
                                background: '#ffffff',
                                padding: '4px 8px',
                                borderRadius: '4px',
                                border: '1px solid #e2e8f0'
                            }
                        }, '[dark_mode_toggle style="' + attributes.style + '" size="' + attributes.size + '" show_label="' + (attributes.showLabel ? 'true' : 'false') + '" align="' + attributes.align + '"]')
                    ]),
                    createElement('button', {
                        key: 'button',
                        style: {
                            display: 'inline-flex',
                            alignItems: 'center',
                            gap: '8px',
                            padding: attributes.size === 'small' ? '6px 12px' : attributes.size === 'large' ? '12px 20px' : '8px 16px',
                            background: attributes.style === 'pill' ? '#2d5f3f' : 'transparent',
                            color: attributes.style === 'pill' ? 'white' : '#2d5f3f',
                            border: attributes.style === 'icon' ? 'none' : '2px solid #2d5f3f',
                            borderRadius: attributes.style === 'pill' ? '25px' : '10px',
                            cursor: 'not-allowed',
                            fontSize: attributes.size === 'small' ? '14px' : attributes.size === 'large' ? '18px' : '16px'
                        }
                    }, [
                        createElement('span', {
                            key: 'icon',
                            innerHTML: '☀️'
                        }),
                        attributes.showLabel && attributes.style !== 'icon' ? createElement('span', { key: 'label' }, 'Toggle Theme') : null
                    ]),
                    createElement('p', {
                        key: 'note',
                        style: {
                            marginTop: '12px',
                            fontSize: '13px',
                            color: '#64748b',
                            fontStyle: 'italic'
                        }
                    }, 'Preview only - fully functional on frontend')
                ])
            ]);
        },
        
        save: function(props) {
            const { attributes } = props;
            const shortcode = '[dark_mode_toggle style="' + attributes.style + '" size="' + attributes.size + '" show_label="' + (attributes.showLabel ? 'true' : 'false') + '" align="' + attributes.align + '"]';
            return createElement('div', { dangerouslySetInnerHTML: { __html: shortcode } });
        }
    });
    
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
);