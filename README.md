# MP Wan Batch

Make batch request to Billplz API from CSV files.

## Limitation

Only support CSV file with UTF-8 encoding. Make sure to have columns as per example.

## Execution

Ensure to set unique Reference ID before the execution. This to avoid duplication.

### Performing Mass Payment Instructions

```bash
    php BatchMPI.php list.csv <api-key-here> <production/sandbox>
    # example: php BatchMPI.php list.csv apikey production
```

### Performing Batch Account Verification

```bash
    php BatchAccountVerification.php list_need_verification.csv <api-key-here> <production/sandbox>
    # example: php BatchAccountVerification.php list_need_verification.csv apikey production
```

### Performing Batch Payment Orders

```bash
    php BatchPO.php list.csv <api-key-here> <production/sandbox>
    # example: php BatchPO.php list.csv apikey production
```
