#!/bin/bash

# Simple Website Stress Test Script
# =================================
# This script performs basic stress testing using curl and GNU parallel
# 
# Usage: ./stress_test.sh [URL] [CONCURRENT_REQUESTS] [DURATION_SECONDS]
# Example: ./stress_test.sh http://localhost/bpsms 5 30

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default values
DEFAULT_URL="http://localhost/bpsms"
DEFAULT_CONCURRENT=5
DEFAULT_DURATION=30

# Get parameters
URL=${1:-$DEFAULT_URL}
CONCURRENT=${2:-$DEFAULT_CONCURRENT}
DURATION=${3:-$DEFAULT_DURATION}

# Test pages
PAGES=(
    "/"
    "/?p=products"
    "/?p=services"
    "/?p=about"
    "/cart.php"
    "/login.php"
    "/register.php"
    "/?p=products&search=motorcycle"
)

# Results file
RESULTS_FILE="stress_test_results_$(date +%Y%m%d_%H%M%S).txt"
SUMMARY_FILE="stress_test_summary_$(date +%Y%m%d_%H%M%S).txt"

echo -e "${BLUE}🔧 BPSMS Website Stress Test Tool${NC}"
echo "========================================"
echo -e "${YELLOW}🌐 Target URL: ${URL}${NC}"
echo -e "${YELLOW}📊 Concurrent Requests: ${CONCURRENT}${NC}"
echo -e "${YELLOW}⏱️  Duration: ${DURATION} seconds${NC}"
echo ""

# Check if required tools are available
check_dependencies() {
    local missing_deps=()
    
    if ! command -v curl &> /dev/null; then
        missing_deps+=("curl")
    fi
    
    if ! command -v parallel &> /dev/null; then
        missing_deps+=("parallel")
    fi
    
    if [ ${#missing_deps[@]} -ne 0 ]; then
        echo -e "${RED}❌ Missing dependencies: ${missing_deps[*]}${NC}"
        echo -e "${YELLOW}📦 Please install them:${NC}"
        for dep in "${missing_deps[@]}"; do
            case $dep in
                "curl")
                    echo "   - Ubuntu/Debian: sudo apt-get install curl"
                    echo "   - CentOS/RHEL: sudo yum install curl"
                    echo "   - macOS: brew install curl"
                    ;;
                "parallel")
                    echo "   - Ubuntu/Debian: sudo apt-get install parallel"
                    echo "   - CentOS/RHEL: sudo yum install parallel"
                    echo "   - macOS: brew install parallel"
                    ;;
            esac
        done
        exit 1
    fi
}

# Function to make a single request
make_request() {
    local page="$1"
    local url="${URL}${page}"
    local start_time=$(date +%s.%N)
    
    # Make the request and capture response
    response=$(curl -s -w "%{http_code}|%{time_total}|%{size_download}" -o /dev/null "$url" 2>/dev/null)
    
    local end_time=$(date +%s.%N)
    local duration=$(echo "$end_time - $start_time" | bc -l)
    local duration_ms=$(echo "$duration * 1000" | bc -l)
    
    # Parse response
    local http_code=$(echo "$response" | cut -d'|' -f1)
    local time_total=$(echo "$response" | cut -d'|' -f2)
    local size_download=$(echo "$response" | cut -d'|' -f3)
    
    # Determine success
    local success=0
    if [ "$http_code" -ge 200 ] && [ "$http_code" -lt 400 ]; then
        success=1
    fi
    
    # Output result
    echo "$(date '+%Y-%m-%d %H:%M:%S.%3N')|$page|$http_code|$duration_ms|$success|$size_download"
}

# Export function for parallel
export -f make_request
export URL

# Run the stress test
run_stress_test() {
    echo -e "${BLUE}🚀 Starting stress test...${NC}"
    echo "⏳ Running for ${DURATION} seconds with ${CONCURRENT} concurrent requests"
    echo ""
    
    # Create a temporary script for parallel execution
    cat > /tmp/stress_test_parallel.sh << 'EOF'
#!/bin/bash
make_request() {
    local page="$1"
    local url="${URL}${page}"
    local start_time=$(date +%s.%N)
    
    response=$(curl -s -w "%{http_code}|%{time_total}|%{size_download}" -o /dev/null "$url" 2>/dev/null)
    
    local end_time=$(date +%s.%N)
    local duration=$(echo "$end_time - $start_time" | bc -l)
    local duration_ms=$(echo "$duration * 1000" | bc -l)
    
    local http_code=$(echo "$response" | cut -d'|' -f1)
    local time_total=$(echo "$response" | cut -d'|' -f2)
    local size_download=$(echo "$response" | cut -d'|' -f3)
    
    local success=0
    if [ "$http_code" -ge 200 ] && [ "$http_code" -lt 400 ]; then
        success=1
    fi
    
    echo "$(date '+%Y-%m-%d %H:%M:%S.%3N')|$page|$http_code|$duration_ms|$success|$size_download"
}
export -f make_request
export URL

# Run parallel requests
timeout ${DURATION} parallel -j ${CONCURRENT} make_request ::: "${PAGES[@]}" > ${RESULTS_FILE}
EOF
    
    chmod +x /tmp/stress_test_parallel.sh
    /tmp/stress_test_parallel.sh
    
    # Clean up
    rm -f /tmp/stress_test_parallel.sh
}

# Generate report
generate_report() {
    if [ ! -f "$RESULTS_FILE" ] || [ ! -s "$RESULTS_FILE" ]; then
        echo -e "${RED}❌ No results found!${NC}"
        return 1
    fi
    
    echo -e "${BLUE}📊 Generating report...${NC}"
    echo ""
    
    # Calculate statistics
    local total_requests=$(wc -l < "$RESULTS_FILE")
    local successful_requests=$(awk -F'|' '$5==1' "$RESULTS_FILE" | wc -l)
    local failed_requests=$((total_requests - successful_requests))
    
    # Calculate response times
    local avg_time=$(awk -F'|' '{sum+=$4; count++} END {if(count>0) print sum/count; else print 0}' "$RESULTS_FILE")
    local min_time=$(awk -F'|' 'NR==1{min=$4} {if($4<min) min=$4} END {print min}' "$RESULTS_FILE")
    local max_time=$(awk -F'|' 'NR==1{max=$4} {if($4>max) max=$4} END {print max}' "$RESULTS_FILE")
    
    # Calculate requests per second
    local start_time=$(head -1 "$RESULTS_FILE" | cut -d'|' -f1)
    local end_time=$(tail -1 "$RESULTS_FILE" | cut -d'|' -f1)
    local duration_sec=$(echo "($(date -d "$end_time" +%s) - $(date -d "$start_time" +%s))" | bc -l)
    local rps=$(echo "scale=2; $total_requests / $duration_sec" | bc -l)
    
    # Generate summary
    cat > "$SUMMARY_FILE" << EOF
BPSMS Website Stress Test Report
===============================
Test Date: $(date)
Target URL: $URL
Duration: ${DURATION} seconds
Concurrent Requests: $CONCURRENT

SUMMARY
-------
Total Requests: $total_requests
Successful: $successful_requests ($(echo "scale=1; $successful_requests * 100 / $total_requests" | bc -l)%)
Failed: $failed_requests ($(echo "scale=1; $failed_requests * 100 / $total_requests" | bc -l)%)
Requests/Second: $rps

RESPONSE TIME STATISTICS
------------------------
Average: $(printf "%.2f" $avg_time)ms
Fastest: $(printf "%.2f" $min_time)ms
Slowest: $(printf "%.2f" $max_time)ms

PAGE-BY-PAGE RESULTS
--------------------
EOF
    
    # Page-by-page analysis
    for page in "${PAGES[@]}"; do
        local page_requests=$(grep "|$page|" "$RESULTS_FILE" | wc -l)
        local page_successful=$(grep "|$page|" "$RESULTS_FILE" | awk -F'|' '$5==1' | wc -l)
        local page_avg_time=$(grep "|$page|" "$RESULTS_FILE" | awk -F'|' '{sum+=$4; count++} END {if(count>0) print sum/count; else print 0}')
        
        if [ $page_requests -gt 0 ]; then
            local success_rate=$(echo "scale=1; $page_successful * 100 / $page_requests" | bc -l)
            echo "Page: $page" >> "$SUMMARY_FILE"
            echo "  Requests: $page_requests | Success: $page_successful ($success_rate%) | Avg Time: $(printf "%.2f" $page_avg_time)ms" >> "$SUMMARY_FILE"
        fi
    done
    
    # Error analysis
    echo "" >> "$SUMMARY_FILE"
    echo "ERROR ANALYSIS" >> "$SUMMARY_FILE"
    echo "---------------" >> "$SUMMARY_FILE"
    
    local error_codes=$(awk -F'|' '$5==0 {print $3}' "$RESULTS_FILE" | sort | uniq -c | sort -nr)
    if [ -n "$error_codes" ]; then
        echo "$error_codes" >> "$SUMMARY_FILE"
    else
        echo "No errors found!" >> "$SUMMARY_FILE"
    fi
    
    # Display summary
    echo -e "${GREEN}✅ Stress test completed!${NC}"
    echo ""
    echo -e "${BLUE}📊 SUMMARY${NC}"
    echo "=========="
    echo -e "${YELLOW}📈 Total Requests: $total_requests${NC}"
    echo -e "${GREEN}✅ Successful: $successful_requests ($(echo "scale=1; $successful_requests * 100 / $total_requests" | bc -l)%)${NC}"
    echo -e "${RED}❌ Failed: $failed_requests ($(echo "scale=1; $failed_requests * 100 / $total_requests" | bc -l)%)${NC}"
    echo -e "${YELLOW}🚀 Requests/Second: $rps${NC}"
    echo ""
    echo -e "${BLUE}📊 RESPONSE TIME STATISTICS${NC}"
    echo "=============================="
    echo -e "${YELLOW}⚡ Average: $(printf "%.2f" $avg_time)ms${NC}"
    echo -e "${GREEN}🏃 Fastest: $(printf "%.2f" $min_time)ms${NC}"
    echo -e "${RED}🐌 Slowest: $(printf "%.2f" $max_time)ms${NC}"
    echo ""
    
    # Performance recommendations
    echo -e "${BLUE}💡 PERFORMANCE RECOMMENDATIONS${NC}"
    echo "================================"
    
    if (( $(echo "$avg_time > 2000" | bc -l) )); then
        echo -e "${YELLOW}⚠️  Average response time is high (>2s). Consider optimizing database queries.${NC}"
    fi
    
    if (( $(echo "$failed_requests > $total_requests * 0.1" | bc -l) )); then
        echo -e "${YELLOW}⚠️  High failure rate (>10%). Check server resources and error logs.${NC}"
    fi
    
    if (( $(echo "$rps < 10" | bc -l) )); then
        echo -e "${YELLOW}⚠️  Low throughput (<10 req/s). Consider server optimization.${NC}"
    fi
    
    if (( $(echo "$max_time > 10000" | bc -l) )); then
        echo -e "${YELLOW}⚠️  Some requests are very slow (>10s). Check for blocking operations.${NC}"
    fi
    
    if (( $(echo "$avg_time <= 1000" | bc -l) )) && (( $(echo "$failed_requests < $total_requests * 0.05" | bc -l) )); then
        echo -e "${GREEN}✅ Performance looks good! Your website is handling the load well.${NC}"
    fi
    
    echo ""
    echo -e "${BLUE}📄 Detailed results saved to: $RESULTS_FILE${NC}"
    echo -e "${BLUE}📄 Summary report saved to: $SUMMARY_FILE${NC}"
}

# Main execution
main() {
    echo -e "${BLUE}🔧 BPSMS Website Stress Test Tool${NC}"
    echo "========================================"
    
    # Check dependencies
    check_dependencies
    
    # Run the test
    run_stress_test
    
    # Generate report
    generate_report
}

# Handle Ctrl+C
trap 'echo -e "\n${YELLOW}🛑 Test interrupted by user${NC}"; exit 1' INT

# Run main function
main "$@"
