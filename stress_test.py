#!/usr/bin/env python3
"""
Simple Stress Test Script for BPSMS Website
===========================================

This script performs basic stress testing on your website by:
1. Making multiple concurrent requests to key pages
2. Testing different endpoints (home, products, cart, etc.)
3. Measuring response times and success rates
4. Generating a simple report

Usage:
    python stress_test.py [options]

Requirements:
    pip install requests aiohttp asyncio
"""

import asyncio
import aiohttp
import time
import json
import argparse
from datetime import datetime
from urllib.parse import urljoin
import sys

class WebsiteStressTest:
    def __init__(self, base_url, max_concurrent=10, test_duration=30):
        self.base_url = base_url.rstrip('/')
        self.max_concurrent = max_concurrent
        self.test_duration = test_duration
        self.results = []
        self.start_time = None
        self.end_time = None
        
        # Key pages to test
        self.test_pages = [
            '/',  # Home page
            '/?p=products',  # Products page
            '/?p=services',  # Services page
            '/?p=about',  # About page
            '/cart.php',  # Cart page
            '/login.php',  # Login page
            '/register.php',  # Register page
            '/?p=products&search=motorcycle',  # Search functionality
        ]
        
        # API endpoints to test (if any)
        self.api_endpoints = [
            '/classes/Master.php?f=get_notifications_count',
            '/classes/Master.php?f=get_notifications',
        ]

    async def make_request(self, session, url, page_name):
        """Make a single HTTP request and record results"""
        start_time = time.time()
        try:
            async with session.get(url, timeout=10) as response:
                end_time = time.time()
                response_time = (end_time - start_time) * 1000  # Convert to milliseconds
                
                result = {
                    'url': url,
                    'page': page_name,
                    'status_code': response.status,
                    'response_time': response_time,
                    'success': 200 <= response.status < 400,
                    'timestamp': datetime.now().isoformat(),
                    'content_length': len(await response.text()) if response.status == 200 else 0
                }
                
                return result
                
        except asyncio.TimeoutError:
            end_time = time.time()
            response_time = (end_time - start_time) * 1000
            
            return {
                'url': url,
                'page': page_name,
                'status_code': 'TIMEOUT',
                'response_time': response_time,
                'success': False,
                'timestamp': datetime.now().isoformat(),
                'content_length': 0
            }
            
        except Exception as e:
            end_time = time.time()
            response_time = (end_time - start_time) * 1000
            
            return {
                'url': url,
                'page': page_name,
                'status_code': 'ERROR',
                'response_time': response_time,
                'success': False,
                'timestamp': datetime.now().isoformat(),
                'content_length': 0,
                'error': str(e)
            }

    async def run_stress_test(self):
        """Run the main stress test"""
        print(f"🚀 Starting stress test on {self.base_url}")
        print(f"📊 Testing {len(self.test_pages)} pages with {self.max_concurrent} concurrent requests")
        print(f"⏱️  Duration: {self.test_duration} seconds")
        print("-" * 60)
        
        self.start_time = time.time()
        
        # Create semaphore to limit concurrent requests
        semaphore = asyncio.Semaphore(self.max_concurrent)
        
        async def limited_request(session, url, page_name):
            async with semaphore:
                return await self.make_request(session, url, page_name)
        
        # Run the test
        async with aiohttp.ClientSession() as session:
            tasks = []
            end_time = self.start_time + self.test_duration
            
            while time.time() < end_time:
                # Create tasks for all test pages
                for page in self.test_pages:
                    url = urljoin(self.base_url, page)
                    task = asyncio.create_task(
                        limited_request(session, url, page)
                    )
                    tasks.append(task)
                
                # Wait a bit before creating more tasks
                await asyncio.sleep(0.1)
            
            # Wait for all tasks to complete
            print("⏳ Waiting for requests to complete...")
            results = await asyncio.gather(*tasks, return_exceptions=True)
            
            # Filter out exceptions and collect results
            for result in results:
                if isinstance(result, dict):
                    self.results.append(result)
        
        self.end_time = time.time()

    def generate_report(self):
        """Generate a comprehensive test report"""
        if not self.results:
            print("❌ No results to report!")
            return
        
        total_requests = len(self.results)
        successful_requests = sum(1 for r in self.results if r.get('success', False))
        failed_requests = total_requests - successful_requests
        
        # Calculate response time statistics
        response_times = [r['response_time'] for r in self.results if r.get('response_time')]
        avg_response_time = sum(response_times) / len(response_times) if response_times else 0
        min_response_time = min(response_times) if response_times else 0
        max_response_time = max(response_times) if response_times else 0
        
        # Group by page
        page_stats = {}
        for result in self.results:
            page = result['page']
            if page not in page_stats:
                page_stats[page] = {'total': 0, 'success': 0, 'times': []}
            
            page_stats[page]['total'] += 1
            if result.get('success', False):
                page_stats[page]['success'] += 1
            page_stats[page]['times'].append(result['response_time'])
        
        # Calculate requests per second
        actual_duration = self.end_time - self.start_time
        requests_per_second = total_requests / actual_duration if actual_duration > 0 else 0
        
        print("\n" + "="*60)
        print("📊 STRESS TEST REPORT")
        print("="*60)
        print(f"🌐 Target URL: {self.base_url}")
        print(f"⏱️  Test Duration: {actual_duration:.2f} seconds")
        print(f"📈 Total Requests: {total_requests}")
        print(f"✅ Successful: {successful_requests} ({successful_requests/total_requests*100:.1f}%)")
        print(f"❌ Failed: {failed_requests} ({failed_requests/total_requests*100:.1f}%)")
        print(f"🚀 Requests/Second: {requests_per_second:.2f}")
        print()
        
        print("📊 RESPONSE TIME STATISTICS")
        print("-" * 40)
        print(f"⚡ Average: {avg_response_time:.2f}ms")
        print(f"🏃 Fastest: {min_response_time:.2f}ms")
        print(f"🐌 Slowest: {max_response_time:.2f}ms")
        print()
        
        print("📄 PAGE-BY-PAGE RESULTS")
        print("-" * 40)
        for page, stats in page_stats.items():
            success_rate = (stats['success'] / stats['total']) * 100 if stats['total'] > 0 else 0
            avg_time = sum(stats['times']) / len(stats['times']) if stats['times'] else 0
            print(f"📄 {page}")
            print(f"   Requests: {stats['total']} | Success: {stats['success']} ({success_rate:.1f}%) | Avg Time: {avg_time:.2f}ms")
        print()
        
        # Error analysis
        errors = [r for r in self.results if not r.get('success', False)]
        if errors:
            print("❌ ERROR ANALYSIS")
            print("-" * 40)
            error_types = {}
            for error in errors:
                status = error.get('status_code', 'UNKNOWN')
                error_types[status] = error_types.get(status, 0) + 1
            
            for error_type, count in error_types.items():
                print(f"🔴 {error_type}: {count} occurrences")
            print()
        
        # Performance recommendations
        print("💡 PERFORMANCE RECOMMENDATIONS")
        print("-" * 40)
        if avg_response_time > 2000:
            print("⚠️  Average response time is high (>2s). Consider optimizing database queries.")
        if failed_requests > total_requests * 0.1:
            print("⚠️  High failure rate (>10%). Check server resources and error logs.")
        if requests_per_second < 10:
            print("⚠️  Low throughput (<10 req/s). Consider server optimization.")
        if max_response_time > 10000:
            print("⚠️  Some requests are very slow (>10s). Check for blocking operations.")
        
        if avg_response_time <= 1000 and failed_requests < total_requests * 0.05:
            print("✅ Performance looks good! Your website is handling the load well.")
        
        print("\n" + "="*60)

    def save_results(self, filename=None):
        """Save detailed results to JSON file"""
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"stress_test_results_{timestamp}.json"
        
        report_data = {
            'test_info': {
                'base_url': self.base_url,
                'start_time': self.start_time,
                'end_time': self.end_time,
                'duration': self.end_time - self.start_time if self.end_time and self.start_time else 0,
                'max_concurrent': self.max_concurrent,
                'test_duration': self.test_duration
            },
            'summary': {
                'total_requests': len(self.results),
                'successful_requests': sum(1 for r in self.results if r.get('success', False)),
                'failed_requests': len(self.results) - sum(1 for r in self.results if r.get('success', False))
            },
            'results': self.results
        }
        
        with open(filename, 'w') as f:
            json.dump(report_data, f, indent=2)
        
        print(f"💾 Detailed results saved to: {filename}")

async def main():
    parser = argparse.ArgumentParser(description='Simple Website Stress Test')
    parser.add_argument('url', help='Base URL of the website to test')
    parser.add_argument('-c', '--concurrent', type=int, default=10, 
                       help='Maximum concurrent requests (default: 10)')
    parser.add_argument('-d', '--duration', type=int, default=30, 
                       help='Test duration in seconds (default: 30)')
    parser.add_argument('-o', '--output', help='Output file for detailed results')
    parser.add_argument('--quiet', action='store_true', help='Suppress progress output')
    
    args = parser.parse_args()
    
    # Validate URL
    if not args.url.startswith(('http://', 'https://')):
        args.url = 'http://' + args.url
    
    # Create and run stress test
    stress_test = WebsiteStressTest(
        base_url=args.url,
        max_concurrent=args.concurrent,
        test_duration=args.duration
    )
    
    try:
        await stress_test.run_stress_test()
        stress_test.generate_report()
        
        if args.output:
            stress_test.save_results(args.output)
        else:
            stress_test.save_results()
            
    except KeyboardInterrupt:
        print("\n🛑 Test interrupted by user")
        sys.exit(1)
    except Exception as e:
        print(f"❌ Error during stress test: {e}")
        sys.exit(1)

if __name__ == "__main__":
    print("🔧 BPSMS Website Stress Test Tool")
    print("=" * 40)
    
    # Check if required packages are installed
    try:
        import aiohttp
    except ImportError:
        print("❌ Required package 'aiohttp' not found.")
        print("📦 Please install it with: pip install aiohttp")
        sys.exit(1)
    
    asyncio.run(main())
