/**
 * Script handle admin notices.
 *
 * @since 4.1.7.3.2
 * @version 1.0.1
 */
import adminAPI from '../api';
let elAdminTabContent = null;
let dataHtml = null;
const queryString = window.location.search;
const urlParams = new URLSearchParams( queryString );
const tab = urlParams.get( 'tab' );
let elNotifyAction;

const getAddons = ( set = '' ) => {
	const params = tab ? `?tab=${ tab }` : `?${ set }`;
	fetch( adminAPI.apiAddons + params, {
		method: 'GET',
	} ).then( ( res ) =>
		res.json()
	).then( ( res ) => {
		// console.log(data);

		const { status, message, data } = res;
		if ( status === 'success' ) {
			dataHtml = data;
		} else {
			dataHtml = message;
		}
	} ).catch( ( err ) => {
		console.log( err );
	} );
};

const isHandling = [];
const addonsAction = ( data, callBack ) => {
	const addonSlug = data.addon.slug;
	const action = data.action;

	if ( isHandling.indexOf( addonSlug ) !== -1 ) {
		return;
	}
	isHandling.push( addonSlug );

	fetch( adminAPI.apiAddonAction, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify( { ...data } ),
	} ).then( ( res ) =>
		res.json()
	).then( ( res ) => {
		const indexAddonHanding = isHandling.indexOf( addonSlug );
		if ( indexAddonHanding !== -1 ) {
			isHandling.splice( indexAddonHanding, 1 );
		}

		const elAddon = document.querySelector( `#${ addonSlug }` );
		if ( elAddon ) {
			const parent = elAddon.closest( '.lp-toggle-switch' );
			if ( parent ) {
				const dashicons = parent.querySelector( '.dashicons-update' );
				dashicons.style.display = 'none';
				if ( action === 'deactivate' ) {
					elAddon.setAttribute( 'data-action', 'activate' );
				} else if ( action === 'activate' ) {
					elAddon.setAttribute( 'data-action', 'deactivate' );
				}
				const label = parent.querySelector( `label[for=${ addonSlug }]` );
				label.style.display = 'inline-flex';
			}
		}

		const { status, message, data } = res;

		if ( callBack ) {
			callBack( status, message, data );
		}

		handleNotify( status, message );
	} ).catch( ( err ) => {
		handleNotify( 'error', err );
		console.log( err );
	} );
};

const handleNotify = ( status, message ) => {
	const elSuccess = elNotifyAction.querySelector( `.${ elNotifyAction.classList.value }__success` );
	const elFailed = elNotifyAction.querySelector( `.${ elNotifyAction.classList.value }__error` );

	if ( status === 'success' ) {
		elFailed.style.display = 'none';
		elSuccess.style.display = 'block';
		elSuccess.querySelector( '.message' ).innerHTML = message;
	} else {
		elSuccess.style.display = 'none';
		elFailed.style.display = 'block';
		elFailed.querySelector( '.message' ).innerHTML = message;
	}

	elNotifyAction.classList.add( 'show' );
	setTimeout( () => {
		elNotifyAction.classList.remove( 'show' );
	}, 3000 );
};

// Get addons when js loaded.
getAddons();

/*** DOMContentLoaded ***/
document.addEventListener( 'DOMContentLoaded', () => {
	elAdminTabContent = document.querySelector( '.lp-admin-tab-content' );
	elNotifyAction = document.querySelector( '.lp-notify-action' );

	const interval = setInterval( () => {
		if ( dataHtml !== null ) {
			if ( dataHtml.length > 0 ) {
				elAdminTabContent.innerHTML = dataHtml;
			}

			const elNavTabWrapper = document.querySelector( '.lp-nav-tab-wrapper' );
			const elNavTabWrapperClone = elNavTabWrapper.cloneNode( true );
			const elxx = document.querySelector( '.lp-admin-tab-content' );
			elxx.insertBefore( elNavTabWrapperClone, elxx.children[ 0 ] );
			elNavTabWrapperClone.style.display = 'flex';
			elNavTabWrapper.remove();

			clearInterval( interval );
		}
	}, 1 );
} );

/*** Events ***/
document.addEventListener( 'click', ( e ) => {
	const el = e.target;
	const tagName = el.tagName.toLowerCase();
	if ( tagName === 'span' ) {
		e.preventDefault();
		const elBtnAction = el.closest( '.btn-addon-action' );
		if ( elBtnAction ) {
			elBtnAction.click();
		}
	}

	// Events actions: activate, deactivate.
	if ( el.classList.contains( 'lp-toggle-switch-label' ) ) {
		//e.preventDefault();

		const idLabel = el.getAttribute( 'for' );
		const elInput = document.querySelector( `#${ idLabel }` );
		const action = elInput.getAttribute( 'data-action' );
		const addon = JSON.parse( elInput.getAttribute( 'data-addon' ) );
		const parent = el.closest( '.lp-toggle-switch' );
		const label = parent.querySelector( `label[for=${ idLabel }]` );
		const dashicons = parent.querySelector( '.dashicons-update' );
		dashicons.style.display = 'inline-block';
		label.style.display = 'none';
		const data = { action, addon };
		addonsAction( data );
	}

	// Events actions: install, update, delete.
	if ( el.classList.contains( 'btn-addon-action' ) ) {
		e.preventDefault();
		el.classList.add( 'handling' );
		const addon = JSON.parse( el.getAttribute( 'data-addon' ) );
		const action = el.getAttribute( 'data-action' );
		const elAddonItem = el.closest( '.lp-addon-item' );
		const elItemPurchase = elAddonItem.querySelector( '.lp-addon-item__purchase' );

		if ( action === 'purchase' ) {
			elItemPurchase.style.display = 'block';
			return;
		} else if ( action === 'buy' ) {
			const link = el.dataset.link;
			window.open( link, '_blank' );
			return;
		} else if ( action === 'cancel' ) {
			elItemPurchase.style.display = 'none';
			return;
		}

		// Send request to server.
		const purchase = el.closest( '.lp-addon-item__purchase' );
		let purchaseCode = '';
		if ( purchase ) {
			purchaseCode = purchase.querySelector( 'input' ).value;
		}

		const data = { purchase_code: purchaseCode, action, addon };
		addonsAction( data, function( status, message, data ) {
			if ( status === 'success' ) {

			}

			el.classList.remove( 'handling' );
		} );
	}
} );
