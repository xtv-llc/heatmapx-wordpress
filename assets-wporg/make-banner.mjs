import sharp from '/Users/tomoyatokudome/Documents/heatmapx/node_modules/sharp/lib/index.js'
import { readFileSync, writeFileSync } from 'node:fs'

const logo = readFileSync('/Users/tomoyatokudome/Documents/heatmapx/assets/brand/heatmapx-logo-horizontal-transparent.png').toString('base64')

// 明るい背景×透過ロゴ（濃紺文字）×右側にヒートマップの熱だまり
const svg = `
<svg xmlns="http://www.w3.org/2000/svg" width="1544" height="500" viewBox="0 0 1544 500">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#ffffff"/>
      <stop offset="1" stop-color="#fff7ed"/>
    </linearGradient>
    <radialGradient id="heat1" cx="0.5" cy="0.5" r="0.5">
      <stop offset="0" stop-color="#ef4444" stop-opacity="0.85"/>
      <stop offset="0.45" stop-color="#f97316" stop-opacity="0.55"/>
      <stop offset="0.75" stop-color="#facc15" stop-opacity="0.25"/>
      <stop offset="1" stop-color="#facc15" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="heat2" cx="0.5" cy="0.5" r="0.5">
      <stop offset="0" stop-color="#f97316" stop-opacity="0.65"/>
      <stop offset="0.6" stop-color="#facc15" stop-opacity="0.3"/>
      <stop offset="1" stop-color="#facc15" stop-opacity="0"/>
    </radialGradient>
    <radialGradient id="heat3" cx="0.5" cy="0.5" r="0.5">
      <stop offset="0" stop-color="#22c55e" stop-opacity="0.4"/>
      <stop offset="1" stop-color="#22c55e" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <rect width="1544" height="500" fill="url(#bg)"/>
  <!-- うっすらグリッド（計測対象ページの気配） -->
  <g stroke="#0f172a" stroke-opacity="0.05" stroke-width="2">
    ${Array.from({length: 15}, (_, i) => `<line x1="${(i+1)*96.5}" y1="0" x2="${(i+1)*96.5}" y2="500"/>`).join('')}
    ${Array.from({length: 4}, (_, i) => `<line x1="0" y1="${(i+1)*100}" x2="1544" y2="${(i+1)*100}"/>`).join('')}
  </g>
  <!-- ヒートマップの熱だまり（右寄せ） -->
  <ellipse cx="1270" cy="140" rx="340" ry="240" fill="url(#heat1)"/>
  <ellipse cx="1060" cy="420" rx="270" ry="190" fill="url(#heat2)"/>
  <ellipse cx="1460" cy="430" rx="210" ry="160" fill="url(#heat3)"/>
  <!-- クリック点の粒 -->
  <g fill="#7c2d12">
    <circle cx="1267" cy="142" r="7" fill-opacity="0.85"/>
    <circle cx="1228" cy="178" r="5" fill-opacity="0.6"/>
    <circle cx="1312" cy="118" r="5" fill-opacity="0.6"/>
    <circle cx="1062" cy="418" r="6" fill-opacity="0.7"/>
    <circle cx="1108" cy="390" r="4" fill-opacity="0.5"/>
  </g>
  <!-- 透過ロゴ（左側） -->
  <image href="data:image/png;base64,${logo}" x="70" y="80" width="800" height="268"/>
  <!-- タグライン -->
  <text x="140" y="410" font-family="Helvetica, Arial, sans-serif" font-size="42" font-weight="500" fill="#334155" letter-spacing="1">Heatmaps &amp; A/B Testing for WordPress</text>
</svg>`

const big = await sharp(Buffer.from(svg), { density: 72 }).png().toBuffer()
writeFileSync('banner-1544x500.png', big)
const small = await sharp(big).resize(772, 250).png().toBuffer()
writeFileSync('banner-772x250.png', small)
console.log('done')
