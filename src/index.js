import { registerBlockVariation } from '@wordpress/blocks'

import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';


registerBlockVariation( 'core/query', {
  name: 'query-listing',
  title: 'Listings',
  icon: 'building',
  description: 'Displays listings CPT.',
  isActive: [ 'namespace' ],
  attributes: {
    namespace: 'query-listing',
    align: 'wide',
    query: {
      postType: 'listing',
      perPage: 3,
      pages: 1,
      order: 'asc',
      orderBy: 'title',
      offset: 0,
      exclude: [],
      inherit: false,
    },
  },
  allowedControls: [
    'order',
    'taxQuery',
  ],
  innerBlocks: [
    [
      'core/post-template', { 'layout': { type: 'grid', columnCount: 3 } },
      [
        [ 'core/post-featured-image' ],
        [ 'core/post-title', { level: 3, isLink: true } ]
      ],
    ]
  ]
} )


export const withListingControls = ( BlockEdit ) => ( props ) => {

  // let's deconstruct the nested object here
  const {
    attributes: {
      query,
      namespace,
    },
    setAttributes
  } = props

  return (
    <>
      <BlockEdit {...props} />
      {
        'query-listing' === namespace &&
        <InspectorControls>
          <PanelBody title="Listings Settings">
            <SelectControl
              label="City"
              value={ query.metaCity }
              options={ [
                { value: '', label: 'Select city...' },
                { value: 'athens',  label: 'Athens' },
                { value: 'istanbul',  label: 'Istanbul' },
                { value: 'nyc',  label: 'NYC' },
              ] }
              onChange={ ( value ) => {
                setAttributes( {
                  query: {
                    ...query,
                    metaCity: value
                  }
                } );
              } }
            />
          </PanelBody>
        </InspectorControls>

      }
    </>
  )

}

addFilter( 'editor.BlockEdit', 'core/query', withListingControls );
