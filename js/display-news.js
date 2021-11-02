const el = wp.element.createElement;
const registerBlock = wp.blocks.registerBlockType;
const { CheckboxControl, SelectControl, Icon } = wp.components;
registerBlock('shryoo/display-news', {
  title: 'Recent news',
  icon: 'testimonial',
  category: 'media',
  edit: (props) => {
    const blockTitle = el('div', { style: { display: 'flex', alignItems: 'center', marginBottom: '1rem' } }, [
      el(Icon, { icon: 'testimonial', style: { marginRight: '0.5rem' } }),
      el('p', { style: { fontSize: '1.25rem', margin: '0' } }, 'Recent news'),
    ]);
    const field_numArticles = el(SelectControl, {
      options: [
        { label: '3', value: '3' },
        { label: '6', value: '6' },
        { label: '9', value: '9' },
        { label: '12', value: '12' },
        { label: 'Show all', value: 'ALL' },
      ],
      label: 'Number of articles to display',
      value: props.attributes.numArticles,
      onChange: (value) => props.setAttributes({ numArticles: value }),
    });
    const field_order = el(CheckboxControl, {
      label: 'Descending order (newest articles first)',
      checked: props.attributes.desc,
      onChange: (value) => props.setAttributes({ desc: value }),
    });
    return el('div', {
      style: {
        padding: '1rem',
        border: '1px solid gray',
      }
    }, [blockTitle, field_numArticles, field_order]);
  },
  save: (props) => null,
});