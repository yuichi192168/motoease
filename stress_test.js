#!/usr/bin/env node

/**
 * Simple Website Stress Test Script (Node.js)
 * ============================================
 * 
 * This script performs basic stress testing on your BPSMS website
 * 
 * Usage:
 *   node stress_test.js [URL] [CONCURRENT] [DURATION]
 *   node stress_test.js http://localhost/bpsms 10 30
 * 
 * Requirements:
 *   npm install axios
 */

const axios = require('axios');
const fs = require('fs');
const path = require('path');

class StressTest {
    constructor(baseUrl, maxConcurrent = 10, duration = 30) {
        this.baseUrl = baseUrl.replace(/\/$/, '');
        this.maxConcurrent = maxConcurrent;
        this.duration = duration;
        this.results = [];
        this.startTime = null;
        this.endTime = null;
        
        // Test pages
        this.testPages = [
            '/',
            '/?p=products',
            '/?p=services', 
            '/?p=about',
            '/cart.php',
            '/login.php',
            '/register.php',
            '/?p=products&search=motorcycle'
        ];
    }

    async makeRequest(url, pageName) {
        const startTime = Date.now();
        
        try {
            const response = await axios.get(url, {
                timeout: 10000,
                validateStatus: () => true // Accept all status codes
            });
            
            const endTime = Date.now();
            const responseTime = endTime - startTime;
            
            return {
                url,
                page: pageName,
                statusCode: response.status,
                responseTime,
                success: response.status >= 200 && response.status < 400,
                timestamp: new Date().toISOString(),
                contentLength: response.data ? response.data.length : 0
            };
            
        } catch (error) {
            const endTime = Date.now();
            const responseTime = endTime - startTime;
            
            return {
                url,
                page: pageName,
                statusCode: 'ERROR',
                responseTime,
                success: false,
                timestamp: new Date().toISOString(),
                contentLength: 0,
                error: error.message
            };
        }
    }

    async runTest() {
        console.log('🚀 Starting stress test...');
        console.log(`🌐 Target: ${this.baseUrl}`);
        console.log(`📊 Concurrent: ${this.maxConcurrent}`);
        console.log(`⏱️  Duration: ${this.duration} seconds`);
        console.log('─'.repeat(50));
        
        this.startTime = Date.now();
        const endTime = this.startTime + (this.duration * 1000);
        
        const semaphore = new Semaphore(this.maxConcurrent);
        
        while (Date.now() < endTime) {
            // Create tasks for all pages
            const tasks = this.testPages.map(page => {
                const url = `${this.baseUrl}${page}`;
                return semaphore.acquire().then(async (release) => {
                    try {
                        const result = await this.makeRequest(url, page);
                        this.results.push(result);
                    } finally {
                        release();
                    }
                });
            });
            
            // Wait a bit before creating more tasks
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        this.endTime = Date.now();
    }

    generateReport() {
        if (this.results.length === 0) {
            console.log('❌ No results to report!');
            return;
        }

        const totalRequests = this.results.length;
        const successfulRequests = this.results.filter(r => r.success).length;
        const failedRequests = totalRequests - successfulRequests;
        
        const responseTimes = this.results.map(r => r.responseTime);
        const avgResponseTime = responseTimes.reduce((a, b) => a + b, 0) / responseTimes.length;
        const minResponseTime = Math.min(...responseTimes);
        const maxResponseTime = Math.max(...responseTimes);
        
        const actualDuration = (this.endTime - this.startTime) / 1000;
        const requestsPerSecond = totalRequests / actualDuration;
        
        // Page-by-page analysis
        const pageStats = {};
        this.results.forEach(result => {
            const page = result.page;
            if (!pageStats[page]) {
                pageStats[page] = { total: 0, success: 0, times: [] };
            }
            pageStats[page].total++;
            if (result.success) pageStats[page].success++;
            pageStats[page].times.push(result.responseTime);
        });
        
        // Generate report
        console.log('\n' + '='.repeat(60));
        console.log('📊 STRESS TEST REPORT');
        console.log('='.repeat(60));
        console.log(`🌐 Target URL: ${this.baseUrl}`);
        console.log(`⏱️  Test Duration: ${actualDuration.toFixed(2)} seconds`);
        console.log(`📈 Total Requests: ${totalRequests}`);
        console.log(`✅ Successful: ${successfulRequests} (${(successfulRequests/totalRequests*100).toFixed(1)}%)`);
        console.log(`❌ Failed: ${failedRequests} (${(failedRequests/totalRequests*100).toFixed(1)}%)`);
        console.log(`🚀 Requests/Second: ${requestsPerSecond.toFixed(2)}`);
        console.log();
        
        console.log('📊 RESPONSE TIME STATISTICS');
        console.log('─'.repeat(40));
        console.log(`⚡ Average: ${avgResponseTime.toFixed(2)}ms`);
        console.log(`🏃 Fastest: ${minResponseTime.toFixed(2)}ms`);
        console.log(`🐌 Slowest: ${maxResponseTime.toFixed(2)}ms`);
        console.log();
        
        console.log('📄 PAGE-BY-PAGE RESULTS');
        console.log('─'.repeat(40));
        Object.entries(pageStats).forEach(([page, stats]) => {
            const successRate = (stats.success / stats.total) * 100;
            const avgTime = stats.times.reduce((a, b) => a + b, 0) / stats.times.length;
            console.log(`📄 ${page}`);
            console.log(`   Requests: ${stats.total} | Success: ${stats.success} (${successRate.toFixed(1)}%) | Avg Time: ${avgTime.toFixed(2)}ms`);
        });
        console.log();
        
        // Error analysis
        const errors = this.results.filter(r => !r.success);
        if (errors.length > 0) {
            console.log('❌ ERROR ANALYSIS');
            console.log('─'.repeat(40));
            const errorTypes = {};
            errors.forEach(error => {
                const status = error.statusCode;
                errorTypes[status] = (errorTypes[status] || 0) + 1;
            });
            
            Object.entries(errorTypes).forEach(([type, count]) => {
                console.log(`🔴 ${type}: ${count} occurrences`);
            });
            console.log();
        }
        
        // Performance recommendations
        console.log('💡 PERFORMANCE RECOMMENDATIONS');
        console.log('─'.repeat(40));
        
        if (avgResponseTime > 2000) {
            console.log('⚠️  Average response time is high (>2s). Consider optimizing database queries.');
        }
        if (failedRequests > totalRequests * 0.1) {
            console.log('⚠️  High failure rate (>10%). Check server resources and error logs.');
        }
        if (requestsPerSecond < 10) {
            console.log('⚠️  Low throughput (<10 req/s). Consider server optimization.');
        }
        if (maxResponseTime > 10000) {
            console.log('⚠️  Some requests are very slow (>10s). Check for blocking operations.');
        }
        
        if (avgResponseTime <= 1000 && failedRequests < totalRequests * 0.05) {
            console.log('✅ Performance looks good! Your website is handling the load well.');
        }
        
        console.log('\n' + '='.repeat(60));
    }

    saveResults(filename = null) {
        if (!filename) {
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19);
            filename = `stress_test_results_${timestamp}.json`;
        }
        
        const reportData = {
            testInfo: {
                baseUrl: this.baseUrl,
                startTime: this.startTime,
                endTime: this.endTime,
                duration: this.endTime - this.startTime,
                maxConcurrent: this.maxConcurrent,
                testDuration: this.duration
            },
            summary: {
                totalRequests: this.results.length,
                successfulRequests: this.results.filter(r => r.success).length,
                failedRequests: this.results.filter(r => !r.success).length
            },
            results: this.results
        };
        
        fs.writeFileSync(filename, JSON.stringify(reportData, null, 2));
        console.log(`💾 Detailed results saved to: ${filename}`);
    }
}

// Simple semaphore implementation
class Semaphore {
    constructor(max) {
        this.max = max;
        this.current = 0;
        this.queue = [];
    }
    
    async acquire() {
        return new Promise((resolve) => {
            if (this.current < this.max) {
                this.current++;
                resolve(() => {
                    this.current--;
                    if (this.queue.length > 0) {
                        const next = this.queue.shift();
                        this.current++;
                        next();
                    }
                });
            } else {
                this.queue.push(() => {
                    resolve(() => {
                        this.current--;
                        if (this.queue.length > 0) {
                            const next = this.queue.shift();
                            this.current++;
                            next();
                        }
                    });
                });
            }
        });
    }
}

// Main execution
async function main() {
    const args = process.argv.slice(2);
    const url = args[0] || 'http://localhost/bpsms';
    const concurrent = parseInt(args[1]) || 10;
    const duration = parseInt(args[2]) || 30;
    
    console.log('🔧 BPSMS Website Stress Test Tool');
    console.log('==================================');
    
    // Check if axios is available
    try {
        require('axios');
    } catch (error) {
        console.log('❌ Required package "axios" not found.');
        console.log('📦 Please install it with: npm install axios');
        process.exit(1);
    }
    
    const stressTest = new StressTest(url, concurrent, duration);
    
    try {
        await stressTest.runTest();
        stressTest.generateReport();
        stressTest.saveResults();
    } catch (error) {
        console.error('❌ Error during stress test:', error.message);
        process.exit(1);
    }
}

// Handle Ctrl+C
process.on('SIGINT', () => {
    console.log('\n🛑 Test interrupted by user');
    process.exit(1);
});

// Run the test
main().catch(console.error);
