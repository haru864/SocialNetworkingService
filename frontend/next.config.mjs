/** @type {import('next').NextConfig} */
const nextConfig = {
    // output: 'export',
    env: {
        // frontDomain: 'http://sns.test.com',
        FRONT_DOMAIN: 'http://localhost:3000',
        API_DOMAIN: 'http://sns.test.com'
    },
    // basePath: '/nextjs',
};

export default nextConfig;
