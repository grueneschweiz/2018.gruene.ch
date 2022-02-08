export default function ajax( url, method, data = null ) {
	return new Promise( function( resolve, reject ) {
		let xhr = new XMLHttpRequest();
		xhr.open( method, url );
		xhr.send( data );
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4) {
				let raw = xhr.responseText;
				let resp;
				try {
					resp = JSON.parse( raw );
				}
				catch ( err ) {
					resp = {};
				}

				if (xhr.status === 200) {
					resolve( resp );
				}
				else {
					reject( resp );
				}
			}
		};
	} );
}
