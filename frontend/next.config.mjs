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
    },
};

export default nextConfig;
