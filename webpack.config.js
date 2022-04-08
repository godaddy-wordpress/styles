const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,

	entry: {
		latest: path.resolve( process.cwd(), 'src', 'index.js' ),
		[ 'wp/5.9.0' ]: path.resolve( process.cwd(), 'src', 'index.scss' ),
	},
};
