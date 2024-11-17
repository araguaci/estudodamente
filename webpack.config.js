/**
 * TerserWebpackPlugin
 *
 * To remove Comments, use this config:
 *
 * webpack.config.js
 *
 * ```js
 * module.exports = {
 *   optimization: {
 *     minimize: true,
 *     minimizer: [
 *       new TerserPlugin({
 *         terserOptions: {
 *           format: {
 *             comments: false,
 *           },
 *         },
 *         extractComments: false,
 *       }),
 *     ],
 *   },
 * };
 * ````
 *
 * see: https://webpack.js.org/plugins/terser-webpack-plugin/#remove-comments
 */

const { readFileSync } = require('fs');
const { resolve } = require('path');

const { EnvironmentPlugin } = require('webpack');
// const WorkerPlugin = require('worker-plugin');
const TerserPlugin = require('terser-webpack-plugin');

const { merge } = require('webpack-merge');
const {
  argv: { mode },
} = require('yargs');

const { name: filename, version } = require('./package.json');

const ASSET_PATH = '/assets/js/';

const envConfig = (() => {
  switch (mode) {
    case 'production':
      return {
        devtool: false,
        plugins: [
          // new WorkerPlugin({ globalObject: 'self' }),
          new EnvironmentPlugin({
            DEBUG: false,
            ASSET_PATH,
            // GET_CLAPS_API: 'https://worker.getclaps.app',
          }),
        ],
      };

    default:
      return {
        devtool: 'source-map',
        plugins: [
          // new WorkerPlugin({ globalObject: 'self' }),
          new EnvironmentPlugin({
            DEBUG: true,
            ASSET_PATH,
            // GET_CLAPS_API: 'https://worker.getclaps.dev',
          }),
        ],
      };
  }
})();

const sharedPreset = {
  modules: false,
  useBuiltIns: 'entry',
  // useBuiltIns: 'usage',
  corejs: 3,
};

const babelPresetLegacy = {
  babelrc: false,
  presets: [
    [
      '@babel/preset-env',
      {
        ...sharedPreset,
        targets: {
          ie: '11',
        },
      },
    ],
  ],
  // plugins: ['@babel/plugin-proposal-class-properties'],
  plugins: ['@babel/plugin-transform-class-properties'],
};

const babelPresetModern = {
  babelrc: false,
  presets: [
    [
      '@babel/preset-env',
      {
        ...sharedPreset,
        targets: {
          esmodules: true,
        },
      },
    ],
  ],
  // plugins: ['@babel/plugin-proposal-class-properties'],
  plugins: ['@babel/plugin-transform-class-properties'],
};

const sharedConfig = {
  entry: resolve('./_js/src/entry.js'),
  output: {
    path: resolve('./assets/js'),
    publicPath: ASSET_PATH,
  },
  resolve: {
    modules: [
      resolve('./_js'),
      resolve('./node_modules'),
      ...(process.env.NODE_PATH ? [resolve(process.env.NODE_PATH)] : []),
    ],
    extensions: ['.json', '.js'],
    symlinks: true,
  },
  optimization: {
    minimizer: [
      new TerserPlugin({
        extractComments: false,
        terserOptions: {
          format: {
            comments: false,
          },
        },
      }),
    ],
    providedExports: true,
    splitChunks: {
      hidePathInfo: true,
      automaticNameDelimiter: '~',
      chunks: 'async',
      minSize: 20000,
      minRemainingSize: 0,
      minChunks: 1,
      maxAsyncRequests: 30,
      maxInitialRequests: 30,
      enforceSizeThreshold: 50000,
      cacheGroups: {
        defaultVendors: {
          test: /[\\/]node_modules[\\/]/,
          priority: -10,
          idHint: 'vendors',
          reuseExistingChunk: true,
        },
        default: {
          minChunks: 2,
          priority: -20,
          usedExports: true,
          reuseExistingChunk: true,
        },
      },
    },
  },
};

module.exports = [
  merge(envConfig, sharedConfig, {
    output: {
      filename: `${filename}-${version}.min.js`,
      chunkFilename: `[name]-${filename}-${version}.min.js`,
      // chunkFilename: `[contenthash]-${filename}-${version}.min.js`,
    },
    module: {
      rules: [
        {
          test: /(\.jsx|\.js)$/,
          use: {
            loader: 'babel-loader',
            options: babelPresetModern,
          },
          resolve: {
            fullySpecified: false,
          },
        },
        {
          test: /modernizr-custom/,
          use: 'null-loader',
        },
        {
          test: /@webcomponents\/(template|url|webcomponents-platform)/,
          use: 'null-loader',
        },
      ],
    },
  }),
  merge(envConfig, sharedConfig, {
    output: {
      filename: `legacy/${filename}-${version}.min.js`,
      chunkFilename: `legacy/[name]-${filename}-${version}.min.js`,
    },
    module: {
      rules: [
        {
          test: /(\.jsx|\.js)$/,
          use: {
            loader: 'babel-loader',
            options: babelPresetLegacy,
          },
          resolve: {
            fullySpecified: false,
          },
        },
      ],
    },
  }),
];
