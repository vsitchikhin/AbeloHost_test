import * as esbuild from 'esbuild';
import { sassPlugin } from 'esbuild-sass-plugin';

const isWatch = process.argv.includes('--watch');
const isProd = process.env.NODE_ENV === 'production';

/** @type {import('esbuild').BuildOptions} */
const options = {
  entryPoints: {
    'css/style': 'src/assets/scss/main.scss',
  },
  bundle: true,
  outdir: 'public',
  plugins: [sassPlugin()],
  minify: isProd,
  sourcemap: !isProd,
  target: ['es2021'],
  logLevel: 'info',
};

if (isWatch) {
  const ctx = await esbuild.context(options);
  await ctx.watch();
} else {
  await esbuild.build(options);
}
