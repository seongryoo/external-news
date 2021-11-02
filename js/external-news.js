const el = wp.element.createElement;
const registerBlock = wp.blocks.registerBlockType;
const { MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { Icon, Button, TextControl } = wp.components;

registerBlock('shryoo/ext-news-data', {
  title: 'External news data',
  icon: 'id-alt',
  attributes: {
    img: { type: 'number', source: 'meta', meta: 'ext_news__img' },
    img_url: { type: 'string', source: 'meta', meta: 'ext_news__img_url' },
    source: { type: 'string', source: 'meta', meta: 'ext_news__source' },
    link: { type: 'string', source: 'meta', meta: 'ext_news__link' },
    date: { type: 'string', source: 'meta', meta: 'ext_news__date' },
  },
  edit: (props) => {
    const icon = el(Icon, { icon: 'upload' });
    const img = el(
      MediaUploadCheck,
      [],
      el(
        MediaUpload,
        {
          onSelect: (media) => props.setAttributes({ img: media.id, img_url: media.url }),
          value: props.attributes.img,
          render: ({ open }) => el(
            Button,
            {
              onClick: open,
              id: 'img-button',
              className: 'upload-button',
            },
            [icon, 'Upload file']
          )
        }
      )
    );
    let placeHolderUrl = '';
    if (externalNews_scriptData && externalNews_scriptData.pluginUrl) {
      placeHolderUrl = externalNews_scriptData.pluginUrl + 'img/blank-image.png';
      console.log(externalNews_scriptData)
    }
    const imgLabel = el('label', { for: 'img-button' }, 'Thumbnail image:');
    const imgPreview = el('img', { id: 'img-display', src: props.attributes.img_url != '' ? props.attributes.img_url : placeHolderUrl });
    const dateControl = el(TextControl, {
      onChange: (value) => props.setAttributes({ date: value }),
      label: 'Date of publication',
      help: 'Hint: You can use a format like Aug 2, 2021',
      value: props.attributes.date,
      placeholder: 'e.g. Aug 2, 2021',
    });
    const sourceControl = el(TextControl, {
      onChange: (value) => props.setAttributes({ source: value }),
      label: 'News source',
      value: props.attributes.source,
      placeholder: 'e.g. The Daily Bugle'
    })
    const linkControl = el(TextControl, {
      onChange: (value) => props.setAttributes({ link: value }),
      label: 'Link to external article:',
      value: props.attributes.link,
      placeholder: 'Start typing...',
    });
    return el('div', {}, [dateControl, sourceControl, linkControl, imgLabel, img, imgPreview]);
  },
  save: (props) => null,
});
