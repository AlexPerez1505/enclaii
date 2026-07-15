const sharp = require('sharp');
const path = require('path');

const input = path.join(__dirname, '..', 'public', 'favicon.png');
const lightOutput = path.join(__dirname, '..', 'public', 'favicon-light.png');
const darkOutput = path.join(__dirname, '..', 'public', 'favicon-dark.png');
const faviconOutput = path.join(__dirname, '..', 'public', 'favicon.png');

async function main() {
  const { data, info } = await sharp(input)
    .ensureAlpha()
    .raw()
    .toBuffer({ resolveWithObject: true });

  const darkData = Buffer.from(data);
  const lightData = Buffer.from(data);

  for (let i = 0; i < data.length; i += info.channels) {
    const r = data[i];
    const g = data[i + 1];
    const b = data[i + 2];
    const isBackground = r < 35 && g < 70 && b < 115;

    if (isBackground) {
      darkData[i + 3] = 0;
      lightData[i + 3] = 0;
      continue;
    }

    const isWhite = r > 200 && g > 200 && b > 200;
    if (isWhite) {
      lightData[i] = 15;
      lightData[i + 1] = 23;
      lightData[i + 2] = 42;
    }
  }

  const rawOptions = { raw: { width: info.width, height: info.height, channels: info.channels } };
  await sharp(lightData, rawOptions).trim().resize(512, 512, { fit: 'contain' }).png().toFile(lightOutput);
  await sharp(darkData, rawOptions).trim().resize(512, 512, { fit: 'contain' }).png().toFile(darkOutput);
  await sharp(lightOutput).png().toFile(faviconOutput);

  console.log('Created transparent favicons.');
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
