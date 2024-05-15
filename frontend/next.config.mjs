/** @type {import('next').NextConfig} */

const apiDomain = 'http://sns.test.com';

const nextConfig = {
    // output: 'export',
    // basePath: '/nextjs',
    env: {
        // FRONT_DOMAIN: 'http://sns.test.com',
        FRONT_DOMAIN: 'http://localhost:3000',
        API_DOMAIN: apiDomain,
        PROFILE_IMAGE_THUMBNAIL_URL: `${apiDomain}/images/profile/thumbnail`,
        PROFILE_IMAGE_UPLOAD_URL: `${apiDomain}/images/profile/upload`,
        TWEET_IMAGE_THUMBNAIL_URL: `${apiDomain}/images/tweet/upload`,
        TWEET_IMAGE_UPLOAD_URL: `${apiDomain}/images/tweet/upload`,
        TWEET_VIDEO_UPLOAD_URL: `${apiDomain}/videos/tweet`,
        MESSAGE_IMAGE_THUMBNAIL_URL: `${apiDomain}/images/dm/upload`,
        MESSAGE_IMAGE_UPLOAD_URL: `${apiDomain}/images/dm/upload`,
        MESSAGE_VIDEO_UPLOAD_URL: `${apiDomain}/videos/dm`,
    },
};

export default nextConfig;
