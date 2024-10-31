import Store from './store';
import { SubjectSubTypeAndSubjectSelect, SubjectSubTypeSelect } from './components';

Store();

( function() { // local scope

	const { registerBlockType } = wp.blocks; // Blocks API
	const { __ } = wp.i18n; // translation functions
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, PanelRow, Panel, TextControl, SelectControl, Button, Notice, ToggleControl, ColorPicker, RadioControl, BaseControl} = wp.components; 
	const { compose } = wp.compose;
	const { serverSideRender: ServerSideRender } = wp;
	const { useSelect } = wp.data;

	/*
	 * Rating form block
	 */
	registerBlockType( 'ratingwp/rating-form', {
		
		// Built-in attributes
		title: __( 'Rating Form', 'ratingwp' ),
		description: __( 'Adds a rating form.', 'ratingwp' ),
		icon: 'star-filled',
		category: 'common',
		keywords: [ __( 'review', 'ratingwp' ), __( 'rating', 'ratingwp' ) ],

		// Built-in functions
		edit: function( props ) {

			let forms = useSelect( 
		    	select => select( 'ratingwp' ).getForms(), [] 
		    );

		    let formOptions = [];
			for (let i = 0; i < forms.length; i++) {
				formOptions.push( { 
					"value" : forms[i].id, 
					"label" : forms[i].name
				} );
			}

			// init post type
			if (props.attributes.useCurrentPostAsSubject && props.attributes.subjectSubType === undefined) {
				props.setAttributes( { 
					subjectType: 'post',
					subjectSubType: wp.data.select('core/editor').getCurrentPostType()
				} );
			}
			
	        return (
	        	<>
		        	<ServerSideRender
				        block="ratingwp/rating-form"
				        attributes={ props.attributes }
				    />

					<InspectorControls>
						<Panel className="rating-form-block-settings">
							<PanelBody title={ __( 'Form settings', 'ratingwp' ) }>
								<PanelRow>
									<SelectControl 
										value={props.attributes.formId} 
										label={ __( "Form", "ratingwp" ) } 
										labelPosition="top" 
										onChange={ ( value) => { 
											props.setAttributes( { formId: parseInt( value ) } ); 
										} }
										options={ formOptions }
									/>
								</PanelRow>
							</PanelBody>
							<PanelBody title={ __( 'Subject details', 'ratingwp' ) }>
								<PanelRow>
									<ToggleControl
								        label={ __( 'Use current post as subject', 'ratingwp' ) }
								        checked={ props.attributes.useCurrentPostAsSubject }
								        onChange={ ( value ) => {
					                		props.setAttributes( { 
					                			useCurrentPostAsSubject: value,
					                			subjectType: 'post',
						                		subjectSubType: wp.data.select('core/editor').getCurrentPostType(),
							                	subjectId: '',
							                	subjectSearch: '' 
					                		} );
								        } }
								    />
								</PanelRow>
								{
									( ! props.attributes.useCurrentPostAsSubject ) && <PanelRow>
										<SelectControl 
											value={ props.attributes.subjectType }
											label={ __( 'Type', 'ratingwp' ) }
											labelPosition="top"
											onChange={ ( value ) => {
					                			props.setAttributes( { 
					                				subjectType: value,
					                				subjectSubType: '',
						                			subjectId: '',
						                			subjectSearch: ''
					                			} );
					              			} }
					              			className={props.className}
					              			options={ [
					              				{ value: 'post', label: __( 'Post', 'ratingwp' ) },
					              				{ value: 'user', label: __( 'User', 'ratingwp' ) },
					              				{ value: 'taxonomy', label: __( 'Taxonomy', 'ratingwp' ) }
					              			] }
					              		/>
									</PanelRow>
								}
								{ 
									! props.attributes.useCurrentPostAsSubject && ( 
										<SubjectSubTypeAndSubjectSelect {...props} /> 
									)
								}
							</PanelBody>
							<PanelBody title={ __( 'Styles', 'ratingwp' ) }>
								<PanelRow>
									<BaseControl label={ __( 'Primary Color', 'ratingwp' ) }>
										<ColorPicker 
										    color={ props.attributes.primaryColor }
										    onChangeComplete={ ( value ) => {
								            	props.setAttributes( { primaryColor: value.hex } );
								          	} }
										/>
										</BaseControl>
									</PanelRow>
							</PanelBody>
						</Panel>
					</InspectorControls>
				</>
	        );
		},

		/**
		 * The save function returns null as this is a dynamic block. The block is rendered 
		 * server side instead.
		 */
		save: function( props ) {
			return null
		}

	} );


	/*
	 * Rating summary block
	 */
	registerBlockType( 'ratingwp/rating-summary', {
		
		// Built-in attributes
		title: __( 'Rating Summary', 'ratingwp' ),
		description: __( 'Adds a rating summary.', 'ratingwp' ),
		icon: 'star-filled',
		category: 'common',
		keywords: [ __( 'review', 'ratingwp' ), __( 'rating', 'ratingwp' ) ],

		// Built-in functions
		edit: function( props ) {

			let forms = useSelect( 
		    	select => select( 'ratingwp' ).getForms(), [] 
		    );

		    let formOptions = [];
			for (let i = 0; i < forms.length; i++) {
				formOptions.push( { 
					"value" : forms[i].id, 
					"label" : forms[i].name
				} );
			}

			// init post type
			if (props.attributes.useCurrentPostAsSubject && props.attributes.subjectSubType === undefined) {
				props.setAttributes( { 
					subjectType: 'post',
					subjectSubType: wp.data.select('core/editor').getCurrentPostType()
				} );
			}

			let showHeader = props.attributes.layout === 'overall';
			let showPrimaryColor = props.attributes.layout === 'details' || props.attributes.resultType === 'star-rating';

	        return (
	        	<>
		        	<ServerSideRender
				        block="ratingwp/rating-summary"
				        attributes={ props.attributes }
				    />

					<InspectorControls>
						<Panel className="rating-summary-block-settings">
							<PanelBody title={ __( 'Form settings', 'ratingwp' ) }>
								<PanelRow>
									<SelectControl 
										value={props.attributes.formId} 
										label={ __( "Form", "ratingwp" ) } 
										labelPosition="top" 
										onChange={ ( value) => { 
											props.setAttributes( { formId: parseInt( value ) } ); 
										} }
										options={ formOptions }
									/>
								</PanelRow>
							</PanelBody>
							<PanelBody title={ __( 'Subject details', 'ratingwp' ) }>
								<PanelRow>
									<ToggleControl
								        label={ __( 'Use current post as subject', 'ratingwp' ) }
								        checked={ props.attributes.useCurrentPostAsSubject }
								        onChange={ ( value ) => {
								        	props.setAttributes( { 
					                			useCurrentPostAsSubject: value,
					                			subjectType: 'post',
						                		subjectSubType: wp.data.select('core/editor').getCurrentPostType(),
							                	subjectId: '',
							                	subjectSearch: '' 
					                		} );
								        } }
								    />
								</PanelRow>
								
								{ 
									! props.attributes.useCurrentPostAsSubject && ( 
										<PanelRow>
											<SelectControl 
												value={ props.attributes.subjectType }
												label={ __( 'Type', 'ratingwp' ) }
												labelPosition="top"
												onChange={ ( value ) => {
						                			props.setAttributes( { 
						                				subjectType: value,
						                				subjectSubType: '',
						                				subjectId: '',
						                				subjectSearch: ''
								                	} );
						              			} }
						              			className={props.className}
						              			options={ [
						              				{ value: 'post', label: __( 'Post', 'ratingwp' ) },
						              				{ value: 'user', label: __( 'User', 'ratingwp' ) },
						              				{ value: 'taxonomy', label: __( 'Taxonomy', 'ratingwp' ) }
						              			] }
						              		/>
										</PanelRow>
									)
								}
								
								{ 
									( ! props.attributes.useCurrentPostAsSubject ) && <SubjectSubTypeAndSubjectSelect {...props} />
								}

							</PanelBody>
							<PanelBody title={ __( 'Styles', 'ratingwp' ) }>
								<PanelRow>
									<RadioControl
										label={ __( 'Layout', 'ratingwp' ) }
										selected={props.attributes.layout}
										options={ [
											{ label: __( 'Overall', 'ratingwp' ), value: 'overall' },
											{ label: __( 'Details', 'ratingwp' ), value: 'details' }
										] }
										onChange={ ( value ) => {
				                			props.setAttributes( { layout: value } );
				              			} }
									/>
								</PanelRow>
								{ props.attributes.layout == 'overall' &&
									<PanelRow>
										<RadioControl
											label={ __( 'Text Align', 'ratingwp' ) }
											selected={props.attributes.textAlign}
											options={ [
												{ label: __( 'Left', 'ratingwp' ), value: 'left' },
												{ label: __( 'Center', 'ratingwp' ), value: 'center' },
												{ label: __( 'Right', 'ratingwp' ), value: 'right' },
											] }
											onChange={ ( value ) => {
					                			props.setAttributes( { textAlign: value } );
					              			} }
										/>
									</PanelRow>
								}
								<PanelRow>
									<RadioControl
										label={ __( 'Result Type', 'ratingwp' ) }
										selected={props.attributes.resultType}
										options={ [
											{ label: __( 'Score', 'ratingwp' ), value: 'score' },
											{ label: __( 'Star Rating', 'ratingwp' ), value: 'star-rating' },
											{ label: __( 'Percentage', 'ratingwp' ), value: 'percentage' },
										] }
										onChange={ ( value ) => {
				                			props.setAttributes( { resultType: value } );
				              			} }
									/>
								</PanelRow>
								{ showHeader &&
									<PanelRow>
										<SelectControl 
											value={ props.attributes.header }
											label={ __( 'Header', 'ratingwp' ) }
											labelPosition="top"
											onChange={ ( value ) => {
					                			props.setAttributes( { header: value } );
					              			} }
					              			options={ [
					              				{ value: 'h1', label: __( 'H1', 'ratingwp' ) },
					              				{ value: 'h2', label: __( 'H2', 'ratingwp' ) },
					              				{ value: 'h3', label: __( 'H3', 'ratingwp' ) }
					              			] }
					              		/>
									</PanelRow>
								}
								{ showPrimaryColor &&
									<PanelRow>
										<BaseControl label={ __( 'Primary Color', 'ratingwp' ) }>
											
											<ColorPicker 
										        color={ props.attributes.primaryColor }
										        onChangeComplete={ ( value ) => {
								                	props.setAttributes( { primaryColor: value.hex } );
								              	} }
										    />
										</BaseControl>
									</PanelRow>
								}
							</PanelBody>
						</Panel>
					</InspectorControls>
				</>
	        );
		},

	});

	/*
	 * Rating list table block
	 *
	 */
	registerBlockType( 'ratingwp/rating-list-table', {
		
		// Built-in attributes
		title: __( 'Rating List Table', 'ratingwp' ),
		description: __( 'Adds a list table of subject ratings.', 'ratingwp' ),
		icon: 'star-filled',
		category: 'common',
		keywords: [ __( 'review', 'ratingwp' ), __( 'rating', 'ratingwp' ) ],

		// Built-in functions
		edit: function( props ) {

			let forms = useSelect( 
		    	select => select( 'ratingwp' ).getForms(), [] 
		    );

		    let formOptions = [];
			for (let i = 0; i < forms.length; i++) {
				formOptions.push( { 
					"value" : forms[i].id, 
					"label" : forms[i].name
				} );
			}

			let showPrimaryColor = props.attributes.resultType === 'star-rating';
			
	        return (
	        	<>
		        	<ServerSideRender
				        block="ratingwp/rating-list-table"
				        attributes={ props.attributes }
				    />

					<InspectorControls>
						<Panel className="rating-list-table-block-settings">
							<PanelBody title={ __( 'Form settings', 'ratingwp' ) }>
								<PanelRow>
									<SelectControl 
										value={props.attributes.formId} 
										label={ __( "Form", "ratingwp" ) } 
										labelPosition="top" 
										onChange={ ( value) => { 
											props.setAttributes( { formId: parseInt( value ) } ); 
										} }
										options={ formOptions }
									/>
								</PanelRow>
							</PanelBody>
							<PanelBody title={ __( 'Subject details', 'ratingwp' ) }>
								<PanelRow>
									<SelectControl 
										value={ props.attributes.subjectType }
										label={ __( 'Type', 'ratingwp' ) }
										labelPosition="top"
										onChange={ ( value ) => {
				                			props.setAttributes( { 
				                				subjectType: value,
				                				subjectSubType: ''
				                			} );
				              			} }
				              			options={ [
					              			{ value: 'post', label: __( 'Post', 'ratingwp' ) },
					              			{ value: 'user', label: __( 'User', 'ratingwp' ) },
					              			{ value: 'taxonomy', label: __( 'Taxonomy', 'ratingwp' ) }
					              		] }
				              		/>
								</PanelRow>
								
								{ 
									props.attributes.subjectType !== 'user' && ( 
										<SubjectSubTypeSelect {...props} />
									)
								}

							</PanelBody>

							<PanelBody title={ __( 'Styles', 'ratingwp' ) }>
								<PanelRow>
									<SelectControl 
										value={ props.attributes.defaultStyle }
										label={ __( 'Default Style', 'ratingwp' ) }
										labelPosition="top"
										onChange={ ( value ) => {
				                			props.setAttributes( { defaultStyle: value } );
				              			} }
				              			options={ [
				              				{ value: null, label: __( 'Not set', 'ratingwp' ) },
				              				{ value: 'default', label: __( 'Default', 'ratingwp' ) },
				              				{ value: 'stripes', label: __( 'Stripes', 'ratingwp' ) }
				              			] }
				              		/>
								</PanelRow>
								<PanelRow>
									<RadioControl
										label={ __( 'Layout', 'ratingwp' ) }
										selected={props.attributes.layout}
										options={ [
											{ label: __( 'Table', 'ratingwp' ), value: 'table' },
										] }
										onChange={ ( value ) => {
				                			props.setAttributes( { layout: value } );
				              			} }
									/>
								</PanelRow>
								<PanelRow>
									<RadioControl
										label={ __( 'Result Type', 'ratingwp' ) }
										selected={props.attributes.resultType}
										options={ [
											{ label: __( 'Score', 'ratingwp' ), value: 'score' },
											{ label: __( 'Star Rating', 'ratingwp' ), value: 'star-rating' },
											{ label: __( 'Percentage', 'ratingwp' ), value: 'percentage' },
										] }
										onChange={ ( value ) => {
				                			props.setAttributes( { resultType: value } );
				              			} }
									/>
								</PanelRow>
								{ showPrimaryColor &&
									<PanelRow>
										<BaseControl label={ __( 'Primary Color', 'ratingwp' ) }>
											
											<ColorPicker 
										        color={ props.attributes.primaryColor }
										        onChangeComplete={ ( value ) => {
								                	props.setAttributes( { primaryColor: value.hex } );
								              	} }
										    />
										</BaseControl>
									</PanelRow>
								}
							</PanelBody>

							<PanelBody title={ __( 'Table settings', 'ratingwp' ) }>
								<PanelRow>
									<ToggleControl
								        label={ __( 'Fixed width table cells', 'ratingwp' ) }
								        checked={ props.attributes.fixedWidthTableCells }
								        onChange={ ( value ) => {
				                			props.setAttributes( { fixedWidthTableCells: value } );
								        } }
								    />
								</PanelRow>
								<PanelRow>
									<ToggleControl
								        label={ __( 'Show Header', 'ratingwp' ) }
								        checked={ props.attributes.showHeader }
								        onChange={ ( value ) => {
				                			props.setAttributes( { showHeader: value } );
								        } }
								    />
								</PanelRow>
								<PanelRow>
									<ToggleControl
								        label={ __( 'Show Rank', 'ratingwp' ) }
								        checked={ props.attributes.showRank }
								        onChange={ ( value ) => {
				                			props.setAttributes( { showRank: value } );
								        } }
								    />
								</PanelRow>
							</PanelBody>
						</Panel>
					</InspectorControls>
				</>
	        );
		},

		/**
		 * The save function returns null as this is a dynamic block. The block is rendered 
		 * server side instead.
		 */
		save: function( props ) {
			return null
		}

	} );

} )();