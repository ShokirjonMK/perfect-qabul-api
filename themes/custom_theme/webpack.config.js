const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const path = require('path')
const file_name = {
    css: '[name].css',
    js: '[name].js',
}

var config = {
    entry: {
        dist: './assets/src/build/_index.js',
        theme: './assets/src/build/_theme.js'
    },
    module: {
        rules: [
            {
                test: /\.(css|less|s[ac]ss)$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                    },
                    {
                        loader: 'css-loader',
                        options: { sourceMap: true }
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            sourceMap: true,
                            postcssOptions: {
                                config: './assets/src/build/postcss.config.js'
                            }
                        }
                    },
                    {
                        loader: 'sass-loader',
                        options: { sourceMap: true }
                    }
                ],
                exclude: '/node_modules/'
            },
            {
                test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[ext]',
                            outputPath: 'fonts/'
                        }
                    }
                ]
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: '/node_modules/'
            }
        ]
    },
    optimization: {
        splitChunks: {
            cacheGroups: {
                vendors: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    chunks: 'all',
                }
            }
        }
    }
}

module.exports = (env, argv) => {
    if (argv.mode === 'development') {
        config.devtool = 'source-map';
        config.mode = 'development';
        file_name.css = 'dev.[name].css';
        file_name.js = 'dev.[name].js';
    }

    if (argv.mode === 'production') {
        config.mode = 'production';
        file_name.css = 'app.[name].css';
        file_name.js = 'app.[name].js';
    }
    
    config.output = {
        filename: file_name.js,
        path: path.resolve(__dirname, './assets/dist'),
        publicPath: './',
    };

    config.plugins = [
        new MiniCssExtractPlugin({
            filename: file_name.css
        }),
    ];

    return config;
}