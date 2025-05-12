<?php require_once '../layout/header.php'?>

<div class="container-xxl d-flex align-items-center justify-content-center">
    <div class="card mx-auto p-4">
        <div class="card-body">
            <h1 class="text-center mb-4">Generate Book Report</h1>

            <form action="books_report.php" method="GET" target="_blank">
                <div class="row">
                    <div class="col-md-12 mb-3 text-center">
                        <label for="year">Year Published (if no year is provided, generate all book records)</label>
                        <input type="text" class="form-control" name="year" id="year" placeholder="Enter year (e.g., 2020)">
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success">Generate book records</button>
                    <a href="../books/index.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>