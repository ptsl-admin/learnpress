
// Rest API load content in Tab Curriculum - Nhamdv.
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

export default function courseCurriculumSkeleton() {
	const Sekeleton = () => {
		const elements = document.querySelectorAll( '.learnpress-course-curriculum' );

		if ( ! elements.length ) {
			return;
		}

		if ( 'IntersectionObserver' in window ) {
			const eleObserver = new IntersectionObserver( ( entries, observer ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						const ele = entry.target;

						getResponse( ele );

						eleObserver.unobserve( ele );
					}
				} );
			} );

			[ ...elements ].map( ( ele ) => eleObserver.observe( ele ) );
		}
	}

	const getResponse = async ( ele ) => {
		const skeleton = ele.querySelector( '.lp-skeleton-animation' );
		const itemID = ele.dataset.id;
		const sectionID = ele.dataset.section;

		try {
			const page = 1;
			const response = await apiFetch( {
				path: addQueryArgs( 'lp/v1/lazy-load/course-curriculum', {
					courseId: lpGlobalSettings.post_id || '',
					page: page,
					sectionID: sectionID || ''
				} ),
				method: 'GET',
			} );

			const { data, status, message, section_ids } = response;

			if ( status  === 'error' ) {
				throw new Error( message || "Error" );
			}

			let returnData = data;

			if ( sectionID ) {
				if ( section_ids && ! section_ids.includes( sectionID ) ) {
					const response2 = await getResponsive( '', page + 1, sectionID );

					if ( response2 ) {
						const { data2, pages2, page2 } = response2;

						await parseContentItems({ele, returnData, sectionID, itemID, data2, pages2, page2});
					}
				} else {
					await parseContentItems({ele, returnData, sectionID, itemID});
				}
 			} else {
				returnData && ele.insertAdjacentHTML( 'beforeend', returnData );
			 }
		} catch ( error ) {
			ele.insertAdjacentHTML( 'beforeend', `<div class="lp-ajax-message error" style="display:block">${ error.message || 'Error: Query lp/v1/lazy-load/course-curriculum' }</div>` );
		}

		skeleton && skeleton.remove();
	};

	const parseContentItems = async ({ ele, returnData, sectionID, itemID, data2, pages2, page2 }) => {
		var parser = new DOMParser();
		var doc = parser.parseFromString(returnData, 'text/html');

		if ( data2 ) {
			const sections = doc.querySelector('.curriculum-sections' );

			const loadMoreBtn = doc.querySelector( '.curriculum-more__button' );

			if ( loadMoreBtn ) {
				if ( pages2 <= page2 ) {
					loadMoreBtn.remove();
				} else {
					loadMoreBtn.dataset.page = page2;
				}
			}

			sections.insertAdjacentHTML( 'beforeend', data2 );
		}

		const section = doc.querySelector( `[data-section-id="${sectionID}"]` );

		if ( section ) {
			const items = section.querySelectorAll( '.course-item' );
			const item_ids = [...items].map( ( item ) => item.dataset.id );
			const sectionContent = section.querySelector( '.section-content' );
			const itemLoadMore = section.querySelector('.section-item__loadmore');

			if ( itemID && ! item_ids.includes( itemID ) ) {
				const responseItem = await getResponsiveItem( '', 2, sectionID, itemID );

				const { data3, pages3, paged3 } = responseItem;

				if ( pages3 <= paged3 ) {
					itemLoadMore.remove();
				} else {
					itemLoadMore.dataset.page = paged3;
				}

				if ( data3 && sectionContent ) {
					sectionContent.insertAdjacentHTML( 'beforeend', data3 );
				}
			}
		}

		ele.insertAdjacentHTML( 'beforeend', doc.body.innerHTML );
	}

	const getResponsiveItem = async ( returnData, paged, sectionID, itemID ) => {
		const response = await apiFetch( {
			path: addQueryArgs( 'lp/v1/lazy-load/course-curriculum-items', {
				sectionId: sectionID || '',
				page: paged,
			} ),
			method: 'GET',
		} );

		const { data, pages, status, message, item_ids } = response;

		if ( status === 'success' ) {
			returnData += data;

			if ( sectionID && item_ids && itemID && ! item_ids.includes( itemID ) ) {
				return getResponsiveItem( returnData, paged + 1, sectionID, itemID );
			}
		}

		return { data3: returnData, pages3: pages, paged3: paged, status3: status, message3: message };
	}

	const getResponsive = async ( returnData, page, sectionID ) => {
		const response = await apiFetch( {
			path: addQueryArgs( 'lp/v1/lazy-load/course-curriculum', {
				courseId: lpGlobalSettings.post_id || '',
				page: page,
				sectionID: sectionID || '',
				loadMore: true,
			} ),
			method: 'GET',
		} );

		const { data, pages, status, message, section_ids } = response;

		if ( status === 'success' ) {
			returnData += data;

			if ( sectionID && section_ids && ! section_ids.includes( sectionID ) ) {
				return getResponsive( returnData, page + 1, sectionID );
			}
		}

		return { data2: returnData, pages2: pages, page2: page, status2: status, message2: message };
	}

	Sekeleton();

	document.addEventListener( 'click',  ( e ) => {
		const sectionBtns = document.querySelectorAll( '.section-item__loadmore' );

		[...sectionBtns].map( async sectionBtn => {
			if ( sectionBtn.contains( e.target ) ) {
				const sectionItem = sectionBtn.parentNode;
				const sectionId = sectionItem.getAttribute( 'data-section-id' );
				const sectionContent = sectionItem.querySelector( '.section-content' );

				const paged = parseInt( sectionBtn.dataset.page );

				sectionBtn.classList.add( 'loading' );

				try {
					const response = await getResponsiveItem( '', paged + 1, sectionId, '' );

					const { data3, pages3, status3, message3 } = response;

					if ( status3 === 'error' ) {
						throw new Error( message3 || "Error" );
					}

					if ( pages3 <= paged + 1 ) {
						sectionBtn.remove();
					} else {
						sectionBtn.dataset.page = paged + 1;
					}

					sectionContent.insertAdjacentHTML( 'beforeend', data3 );
				} catch( e ) {
					sectionContent.insertAdjacentHTML( 'beforeend', `<div class="lp-ajax-message error" style="display:block">${ e.message || 'Error: Query lp/v1/lazy-load/course-curriculum' }</div>` );
				}

				sectionBtn.classList.remove( 'loading' );
			}
		});

		// Load more Sections
		const moreSections = document.querySelectorAll( '.curriculum-more__button' );

		[ ...moreSections ].map( async moreSection => {
			if ( moreSection.contains( e.target ) ) {
				const paged = parseInt( moreSection.dataset.page );

				const sections = moreSection.parentNode.parentNode.querySelector( '.curriculum-sections' );

				if ( paged && sections ) {
					moreSection.classList.add( 'loading' );

					try{
						const response2 = await getResponsive( '', paged + 1, '' );

						const { data2, pages2, status2, message2 } = response2;

						if ( status2 === 'error' ) {
							throw new Error( message2|| "Error" );
						}

						if ( pages2 <= paged + 1 ) {
							moreSection.remove();
						} else {
							moreSection.dataset.page = paged + 1;
						}

						sections.insertAdjacentHTML( 'beforeend', data2 );
					} catch( e ) {
						sections.insertAdjacentHTML( 'beforeend', `<div class="lp-ajax-message error" style="display:block">${ e.message || 'Error: Query lp/v1/lazy-load/course-curriculum' }</div>` );
					}

					moreSection.classList.remove( 'loading' );
				}
			}
		});

		// Show/Hide accordion
		if ( document.querySelector( '.learnpress-course-curriculum' ) ) {
			const sections = document.querySelectorAll( '.section' );

			[ ...sections ].map( section => {
				if ( section.contains( e.target ) ) {
					const toggle = section.querySelector( '.section-left' );

					toggle.contains( e.target ) && section.classList.toggle( 'closed' );
				}
			});
		}
	});
};
