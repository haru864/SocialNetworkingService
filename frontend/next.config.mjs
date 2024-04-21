/** @type {import('next').NextConfig} */
const nextConfig = {
    // output: 'export',
    // basePath: '/nextjs',
    env: {
        // FRONT_DOMAIN: 'http://sns.test.com',
        FRONT_DOMAIN: 'http://localhost:3000',
        API_DOMAIN: 'http://sns.test.com'
    },
};

export default nextConfig;
