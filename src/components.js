import { __ } from '@wordpress/i18n';
import { withSelect, select, useSelect } from '@wordpress/data';
import { compose, withState } from '@wordpress/compose';
import { SelectControl, PanelRow, CheckboxControl, ComboboxControl } from '@wordpress/components';
import { useState } from '@wordpress/element';


/**
 *
 */
export const SubjectSubTypeAndSubjectSelect = withSelect( ( select, props ) => {

	if ( props.attributes.subjectType === 'post' ) {
		
		let postTypes = select('core').getPostTypes({ per_page : -1 });
		let postTypeOptions = [];

		if (postTypes) {
			for (let i = 0; i < postTypes.length; i++) {
				postTypeOptions.push( { 
					"value" : postTypes[i].slug, 
					"label" : postTypes[i].labels.singular_name
				} );
			}
		}

		let posts =  select('core').getEntityRecords( 'postType', props.attributes.subjectSubType, { search : props.attributes.subjectSearch });

		let postOptions = [];
		
		if (posts) {
			
			for (let i = 0; i < posts.length; i++) {

				postOptions.push( { 
					"value" : posts[i].id.toString(), 
					"label" : posts[i].title.raw
				} );
			}

		}

		return {
			subjectSubTypeOptions: postTypeOptions,
			subjectOptions: postOptions
		};
	
	} else if ( props.attributes.subjectType === 'user') {
		
		let users = wp.data.select('core').getUsers({ search : props.attributes.subjectSearch });
		let userOptions = [];

		if (users) {
			
			for (let i = 0; i < users.length; i++) {
				
				userOptions.push( { 
					"value" : users[i].id.toString(), 
					"label" : users[i].name
				} );
			}

		}

		return {
			subjectOptions : userOptions
		}

	} else if ( props.attributes.subjectType === 'taxonomy' ) {

		let taxonomies = wp.data.select('core').getTaxonomies({ per_page : -1 });
		let taxonomyOptions = [];

		if (taxonomies) {
			
			for (let i = 0; i < taxonomies.length; i++) {

				taxonomyOptions.push( { 
					"value" : taxonomies[i].slug, 
					"label" : taxonomies[i].labels.singular_name
				} );
			}

		}

		let taxonomyTerms = wp.data.select( 'core' ).getEntityRecords( 'taxonomy', props.attributes.subjectSubType, { search : props.attributes.subjectSearch } );
		let taxonomyTermOptions = [];

		if (taxonomyTerms) {

			for (let i = 0; i < taxonomyTerms.length; i++) {
				taxonomyTermOptions.push( { 
					"value" : taxonomyTerms[i].id.toString(), 
					"label" : taxonomyTerms[i].name
				} );
			}
		}


		return {
			subjectSubTypeOptions: taxonomyOptions,
			subjectOptions : taxonomyTermOptions
		}

	}

})(( props ) => {

	let subjectSubTypeSelect;
	let subjectSelect;

	if ( props.attributes.subjectType !== 'user') {
		
		if( ! props.subjectSubTypeOptions ) { // still resolving
			subjectSubTypeSelect = 
				<SelectControl 
					label={ __( 'Sub Type', 'ratingwp' ) } 
					labelPosition="top" 
					options={ [ { 'value' : '', 'label' : __( 'Loading...', 'ratingwp' ) } ] }
				/>;
		} else {

			if ( props.attributes.subjectSubType == '' && props.subjectSubTypeOptions && props.subjectSubTypeOptions.length > 0) {
				props.setAttributes({ 
					subjectSubType : props.subjectSubTypeOptions[0].value 
				} );
			}

			subjectSubTypeSelect = 
				<SelectControl 
					value={props.attributes.subjectSubType}
					label={ __( "Sub Type", "ratingwp" ) } 
					labelPosition="top" 
					onChange={ ( value) => { 
						props.setAttributes( { 
							subjectSubType: value,
							subjectId: '',
							subjectSearch: ''
						} ); 
					} }
					options={props.subjectSubTypeOptions}
				/>
		}
	}

	if ( ! props.subjectOptions ) { 	// still resolving
		subjectSelect =
			<ComboboxControl
				label="Subject"
				labelPosition ="top"
				onFilterValueChange={() => {}}
				options={ [ { "value" : "", "label" : __( 'Loading...', 'ratingwp' ) } ] }
			/>;
	} else {
        subjectSelect = 
			<ComboboxControl
				label="Subject"
				labelPosition ="top"
				value={props.attributes.subjectId} 
				onFilterValueChange={( value) => {
					props.setAttributes( { subjectSearch: value } );
				}}
				options={props.subjectOptions}
				onInputChange={(value) =>
					setFilteredOptions({ options }
						.filter(option =>
							option.label.toLowerCase().startsWith(value.toLowerCase())
						)
					)
				}
				onChange={ ( value ) => { 
					props.setAttributes( { subjectId: value } ); 
				} }
			/>;
	}

	return (
		<>
			{ props.attributes.subjectType != 'user' && (
				<PanelRow>
					{subjectSubTypeSelect}
				</PanelRow>
			) }
			<PanelRow>
				{subjectSelect}
			</PanelRow>
		</>
	);

});


/**
 * 
 */
export const SubjectSubTypeSelect = withSelect( ( select, props ) => {

	if ( props.attributes.subjectType === 'post' ) {
		
		let postTypes = select('core').getPostTypes({ per_page : -1 });
		let postTypeOptions = [];

		if (postTypes) {
			
			for (let i = 0; i < postTypes.length; i++) {

				postTypeOptions.push( { 
					"value" : postTypes[i].slug, 
					"label" : postTypes[i].labels.singular_name
				} );
			}

		}

		return {
			subjectSubTypeOptions: postTypeOptions
		};
	
	} else if ( props.attributes.subjectType === 'taxonomy' ) {

		let taxonomies = wp.data.select('core').getTaxonomies({ per_page : -1 });
		let taxonomyOptions = [];

		if (taxonomies) {

			for (let i = 0; i < taxonomies.length; i++) {
				
				taxonomyOptions.push( { 
					"value" : taxonomies[i].slug, 
					"label" : taxonomies[i].labels.singular_name
				} );
			}

		}

		return {
			subjectSubTypeOptions: taxonomyOptions
		}
	}

})(( props ) => {

	let subjectSubTypeSelect;

	if( ! props.subjectSubTypeOptions ) { // still resolving
		subjectSubTypeSelect = 
			<SelectControl 
				label={ __( 'Sub Type', 'ratingwp' ) } 
				labelPosition="top" 
				options={ [ { 'value' : '', 'label' : __( 'Loading...', 'ratingwp' ) } ] }
			/>;
	} else {

			if ( props.attributes.subjectSubType == '' && props.subjectSubTypeOptions && props.subjectSubTypeOptions.length > 0) {
				props.setAttributes({ subjectSubType : props.subjectSubTypeOptions[0].value } );
			}

		subjectSubTypeSelect = 
			<SelectControl 
				value={props.attributes.subjectSubType} 
				label={ __( "Sub Type", "ratingwp" ) } 
				labelPosition="top" 
				onChange={ ( value) => { 
					props.setAttributes( { subjectSubType: value } ); 
				} }
				options={ props.subjectSubTypeOptions }
			/>
	}

	return (
		<>
			<PanelRow>
				{subjectSubTypeSelect}
			</PanelRow>
		</>
	);

});