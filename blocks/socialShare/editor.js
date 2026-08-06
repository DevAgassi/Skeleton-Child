const { createElement: el, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, ToggleControl } = wp.components;
const { addFilter } = wp.hooks;

const NETWORKS = [
  { attr: 'showX',        label: 'X' },
  { attr: 'showFacebook', label: 'Facebook' },
  { attr: 'showLinkedin',  label: 'LinkedIn' },
  { attr: 'showReddit',   label: 'Reddit' },
];

addFilter(
  'blocks.registerBlockType',
  'theme/social-share-controls',
  (settings, name) => {
    if (name !== 'theme/social-share') return settings;

    const OriginalEdit = settings.edit;

    settings.edit = (props) => {
      const { attributes, setAttributes } = props;

      return el(Fragment, null,
        el(InspectorControls, null,
          el(PanelBody, { title: 'Networks', initialOpen: true },
            NETWORKS.map(({ attr, label }) =>
              el(ToggleControl, {
                key: attr,
                label,
                checked: attributes[attr] !== false,
                onChange: (val) => setAttributes({ [attr]: val }),
              })
            )
          )
        ),
        el(OriginalEdit, props)
      );
    };

    return settings;
  }
);
