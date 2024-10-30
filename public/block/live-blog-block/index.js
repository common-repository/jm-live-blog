/**
 * BLOCK: reference-block
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */
import icon from './icon';
import './editor.scss';

const { __ } = wp.i18n;
const {
    PanelColor,
    InspectorControls
} = wp.editor;// Import registerBlockType() from wp.blocks
const { registerBlockType } = wp.blocks;
const {
    ColorPalette,
    PanelBody,
    TextControl,
    SelectControl,
} = wp.components;

//this is where block control componants go! a-ha!
//const { ToggleControl } = InspectorControls;

registerBlockType( 'jm-live-blog/jm-live-blog-block', {
    // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
    title: __('JM Live Blog', 'jm-live-blog' ),
    icon: icon, // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
    category: 'widgets', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
    keywords: [
        __( 'JM Live Blog', 'jm-live-blog' ),
        __( 'live blog', 'jm-live-blog' ),
        __( 'updates', 'jm-live-blog' ),
    ],
    attributes: {
        jm_live_blog_color_scheme: {
            type: 'string',
            default: 'light'
        },
        jm_live_blog_update_color: {
            type: 'string',
            default: '#93060A'
        },
        jm_live_blog_title: {
            type: 'string',
            default: 'Title'
        },
        jm_live_blog_description: {
            type: 'string',
            default: 'Description'
        }
    },

    // The "edit" property must be a valid function.
    edit: props => {

        const { attributes: { jm_live_blog_color_scheme, jm_live_blog_update_color, jm_live_blog_title, jm_live_blog_description },
            className, setAttributes, isSelected } = props;
        const divStyle = {
            backgroundColor: jm_live_blog_update_color
        };


        return [
            <InspectorControls>
                <PanelBody>
                    <TextControl
                        label={ __( 'Live Blog Title', 'jm-live-blog' ) }
                        value={ jm_live_blog_title }
                        onChange={ jm_live_blog_title => setAttributes( { jm_live_blog_title } ) }
                    />
                </PanelBody>
                <PanelBody>
                    <TextControl
                        label={ __( 'Live Blog Description', 'jm-live-blog' ) }
                        value={ jm_live_blog_description }
                        onChange={ jm_live_blog_description => setAttributes( { jm_live_blog_description } ) }
                    />
                </PanelBody>
                <PanelBody>
                    <SelectControl
                        label={ __( 'Color Scheme', 'jm-live-blog' ) }
                        value={ jm_live_blog_color_scheme }
                        options={ [
                            { value: 'lignt', label: __( 'Light', 'jm-live-blog' ) },
                            { value: 'dark', label: __( 'Dark', 'jm-live-blog' ) }
                        ] }
                        onChange={ jm_live_blog_color_scheme => setAttributes( { jm_live_blog_color_scheme } ) }
                    />
                </PanelBody>

            </InspectorControls>,
            <div className={className}>
                <div id="jm-live-blog" className={ [ jm_live_blog_color_scheme, ' jm-live-blog-outer' ] }>
                    <div className="jm-live-blog-inner">
                        <h3 className="jm-live-blog-title">{ jm_live_blog_title }</h3>
                        <p className="jm-live-blog-description">{ jm_live_blog_description }</p>
                        <div className="jm-live-blog-section-outer">
                            <span id="jm-live-blog-new-updates" style={divStyle}>{ __( 'New Updates', 'jm-live-blog' ) }</span>
                            <section className="jm-live-blog-section">
                                <p>{ __( 'You can add live updates using the custom fields below.', 'jm-live-blog' ) }</p>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        ];
    },

    // The "save" property must be specified and must be a valid function.
    //this is what puts the html in the "edit as html" box
    save() {
        return null;
    },
});