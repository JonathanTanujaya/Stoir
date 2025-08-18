# 🚀 StockFlow Performance Management - Quick Reference Guide

## 📊 Performance Monitoring Commands

### Real-time Performance Dashboard
```bash
# Continuous monitoring (refreshes every 30 seconds)
php artisan performance:dashboard

# Single snapshot view
php artisan performance:dashboard --once

# Custom refresh interval
php artisan performance:dashboard --refresh=60
```

### Performance Analysis
```bash
# Basic performance analysis
php artisan performance:analyze

# Detailed analysis with recommendations
php artisan performance:analyze --detailed

# JSON output for automation
php artisan performance:analyze --output=json

# Focus on specific categories
php artisan performance:analyze --category=database
php artisan performance:analyze --category=cache
php artisan performance:analyze --category=api
```

## 🔧 Performance Optimization Commands

### Apply System Optimizations
```bash
# Preview optimizations (dry-run)
php artisan performance:optimize --dry-run

# Apply all optimizations
php artisan performance:optimize --force

# Apply specific optimization type
php artisan performance:optimize --type=cache --force
php artisan performance:optimize --type=queries --force
php artisan performance:optimize --type=indexes --force
```

### Auto-fix N+1 Query Issues
```bash
# Preview N+1 query fixes
php artisan performance:apply-optimizations --dry-run

# Apply fixes to specific model
php artisan performance:apply-optimizations --model=ClaimPenjualan --force

# Apply all optimizations automatically
php artisan performance:apply-optimizations --force
```

## 🧪 Testing & Validation Commands

### API Testing
```bash
# Run comprehensive API tests
php artisan test tests/Feature/ApiEndpointTest.php

# Run with detailed output
php artisan test tests/Feature/ApiEndpointTest.php --verbose
```

### Route Validation
```bash
# List all API endpoints
php artisan route:list --path=api

# Count API endpoints
php artisan route:list --path=api | wc -l

# Search specific endpoints
php artisan route:list --path=api | grep invoice
```

## 📈 System Health Commands

### Cache Management
```bash
# Clear all caches
php artisan cache:clear

# Optimize application caches
php artisan optimize

# Cache configuration and routes
php artisan config:cache && php artisan route:cache
```

### Database Optimization
```bash
# Run database migrations
php artisan migrate

# Check database status
php artisan migrate:status

# Seed database (if needed)
php artisan db:seed
```

## 🎯 Quick Health Checks

### Daily Performance Check
```bash
# Quick system overview
php artisan performance:dashboard --once

# Generate daily report
php artisan performance:analyze --output=json > daily_performance_$(date +%Y%m%d).json
```

### Weekly Optimization
```bash
# Analyze and optimize
php artisan performance:analyze --detailed
php artisan performance:optimize --dry-run
php artisan performance:apply-optimizations --dry-run
```

### Monthly Deep Analysis
```bash
# Comprehensive analysis with all details
php artisan performance:analyze --detailed --output=json > monthly_report_$(date +%Y%m).json

# Apply all available optimizations
php artisan performance:optimize --force
php artisan performance:apply-optimizations --force
```

## 🚨 Troubleshooting Commands

### Performance Issues
```bash
# Debug slow queries
php artisan performance:analyze --category=database --detailed

# Check cache performance
php artisan performance:analyze --category=cache --detailed

# Monitor API performance
php artisan performance:analyze --category=api --detailed
```

### System Issues
```bash
# Clear all caches and restart
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan optimize

# Rebuild optimizations
php artisan config:cache
php artisan route:cache
```

## 📊 Monitoring Integration

### Automated Reporting
```bash
# Create cron job for daily monitoring:
# 0 9 * * * cd /path/to/stockflow && php artisan performance:analyze --output=json > logs/performance_$(date +\%Y\%m\%d).json

# Create cron job for weekly optimization:
# 0 2 * * 1 cd /path/to/stockflow && php artisan performance:optimize --force
```

### Performance Alerts
```bash
# Check if performance score is below threshold
SCORE=$(php artisan performance:analyze --output=json | jq '.overall_score // 0')
if [ $SCORE -lt 70 ]; then
    echo "⚠️ Performance alert: Score is $SCORE/100"
fi
```

## 🏆 Best Practices

### Development Workflow
1. **Before coding:** Run `php artisan performance:dashboard --once`
2. **After changes:** Run `php artisan performance:analyze`
3. **Before deployment:** Run `php artisan performance:optimize --dry-run`
4. **After deployment:** Run `php artisan performance:dashboard --refresh=30`

### Production Maintenance
1. **Daily:** Monitor performance dashboard
2. **Weekly:** Apply safe optimizations  
3. **Monthly:** Comprehensive analysis and optimization
4. **Quarterly:** Review and update performance targets

## 📚 Additional Resources

### Performance Files Location
- Optimization suggestions: `storage/optimization_suggestions.json`
- Performance reports: `performance_final_report.json`
- Phase reports: `PHASE_*_COMPLETION_REPORT.md`
- Project summary: `PROJECT_COMPLETE_SUMMARY.md`

### Key Performance Metrics
- **Target Performance Score:** 80+/100
- **Target Cache Hit Rate:** 90%+
- **Target API Response Time:** <300ms
- **Target N+1 Query Issues:** 0

---

*StockFlow Performance Management Guide*  
*Last updated: 2025-08-18*  
*Version: 1.0* ✅
