/** @type {import('next').NextConfig} */

const apiDomain = 'http://sns.test.com';
const sseDomain = apiDomain;

const nextConfig = {
    env: {
        FRONT_DOMAIN: apiDomain,
        API_DOMAIN: apiDomain,
        PROFILE_IMAGE_THUMBNAIL_URL: `${apiDomain}/images/profile/thumbnail`,
        PROFILE_IMAGE_UPLOAD_URL: `${apiDomain}/images/profile/upload`,
        TWEET_IMAGE_THUMBNAIL_URL: `${apiDomain}/images/tweet/upload`,
        TWEET_IMAGE_UPLOAD_URL: `${apiDomain}/images/tweet/upload`,
        TWEET_VIDEO_UPLOAD_URL: `${apiDomain}/videos/tweet`,
        MESSAGE_IMAGE_THUMBNAIL_URL: `${apiDomain}/images/dm/upload`,
        MESSAGE_IMAGE_UPLOAD_URL: `${apiDomain}/images/dm/upload`,
        MESSAGE_VIDEO_UPLOAD_URL: `${apiDomain}/videos/dm`,
        SSE_NOTIFICATION_URL: `${sseDomain}/sse/notifications`,
        SSE_MESSAGE_URL: `${sseDomain}/sse/message`,
    },
};

export default nextConfig;
