// Generated using webpack-cli https://github.com/webpack/webpack-cli

const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const isProduction = process.env.NODE_ENV == 'production';
const stylesHandler = MiniCssExtractPlugin.loader;

const config = {
	entry: {
		'createPage': './src/createPage/ext.isekai.createPage.js',
		'discover': './src/discover/ext.isekai.discover.js',
        'tile': './src/tile/ext.isekai.tile.js',
	},
    output: {
		filename: '[name]/ext.isekai.[name].js',
		path: path.resolve(__dirname, 'modules')
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name]/ext.isekai.[name].css',
        }),
        // Add your plugins here
        // Learn more about plugins from https://webpack.js.org/configuration/plugins/
    ],
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/i,
                loader: 'babel-loader',
            },
            {
                test: /\.s[ac]ss$/i,
                use: [stylesHandler, 'css-loader', 'postcss-loader', 'sass-loader'],
            },
            {
                test: /\.less$/i,
                use: [stylesHandler, 'css-loader', 'postcss-loader', 'less-loader'],
            },
            {
                test: /\.css$/i,
                use: [stylesHandler, 'css-loader', 'postcss-loader'],
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2|png|jpg|gif)$/i,
                type: 'asset',
            },

            // Add your rules for custom modules here
            // Learn more about loaders from https://webpack.js.org/loaders/
        ],
    },
};

module.exports = () => {
    if (isProduction) {
        config.mode = 'production';
    } else {
        config.mode = 'development';
    }
    return config;
};
