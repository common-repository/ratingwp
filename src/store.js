import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

/**
 * WordPress data store for RatingWP plugin
 */
export default () => {

	const RAWP_DEFAULT_STATE = {
		forms : []
	};

	const actions = {
		
		setForms( forms ) {
			return {
				type: 'SET_FORMS',
				forms,
			};
		},
		
		getForms( path ) {
			return {
				type: 'GET_FORMS',
				path,
			};
		}
	};

	const reducer = ( state = RAWP_DEFAULT_STATE, action ) => {
		
		switch ( action.type ) {
			case 'SET_FORMS': {
			
				return {
					state,
					forms: action.forms,
				};

				break;
			}
			
			default: {
				return state;
			}
		}
	};

	const selectors = {

		getForms( state ) {
			const { forms } = state;
			return forms;
		}

	};

	const controls = {
		
		GET_FORMS( action ) {
			return apiFetch( { path: action.path } );
		}

	};

	const resolvers = {
		
		*getForms() {
			const forms = yield actions.getForms( '/ratingwp/v1/forms/' );
			return actions.setForms( forms );
		}

	};

	const storeConfig = {
		reducer,
		controls,
		selectors,
		resolvers,
		actions
	};

	registerStore( 'ratingwp', storeConfig );
};