<?php

class Cron_Job_Email_Product implements plugin_cronjob_module
{
    public function init()
    {
        add_action('send_daily_product_qty_email', array($this, 'send_email_product_qty_statics'));
    }

    // feature product qty statistics for each site
    function product_qty_sta_for_each_site()
    {
        // start 00:00:00
        $today_start = strtotime("today midnight");
        // current time
        $now = current_time('timestamp');

        // get data order today with status order processing and completed
        $orders = wc_get_orders([
            'limit'        => -1,
            'type'         => 'shop_order',
            'status'       => ['wc-processing', 'wc-completed'],
            'date_created' => $today_start . '...' . $now,
            'return'       => 'ids',
        ]);

        // create values to save data
        // $products save product name and qty sold
        $products = [];
        $total_revenue = 0;
        $total_orders = count($orders);

        // loop through each order to get product qty sold and total revenue
        foreach ($orders as $order_id) {
            $order = wc_get_order($order_id);
            $total_revenue += $order->get_total();

            // loop through each item in the order
            foreach ($order->get_items() as $item_id => $item) {
                $product_name = $item->get_name();
                $quantity = $item->get_quantity();

                if (!isset($products[$product_name])) {
                    $products[$product_name] = ['name' => $product_name, 'qty' => 0];
                }

                $products[$product_name]['qty'] += $quantity;
            }
        }

        // return data
        return [
            'products' => $products,
            'total_revenue' => $total_revenue,
            'total_orders' => $total_orders
        ];
    }

    private function generate_multisite_sales_report_html($report, $total_all_sites, $total_revenue_all, $total_orders_all)
    {
        $date_today = date('d/m/Y');

        $html = '
        <html><head><meta charset="UTF-8">
        <style>
            body { font-family: Arial; }
            table { width:100%; border-collapse: collapse; margin-bottom:25px; }
            th, td { border:1px solid #ddd; padding:10px; }
            th { background:#f5f5f5; }
            .total-site { background:#e6f7ff; font-weight:bold; }
            .total-all { background:#f5f5f5; font-weight:bold; }
        </style>
        </head><body>
        <h2>Daily Product Sales Report – ' . $date_today . '</h2>
        ';

        foreach ($report as $siteData) {
            $html .= '<div class="site-block">';
            $html .= '<table>
                <tr> <h3 style="text-align:center;">' . esc_html($siteData['site']) . '</h3></tr>
                <tr>
                    <th style="width:60%;">Product Name</th>
                    <th style="width:40%; text-align:right;">Sales Quantity</th>
                </tr>';

            if (!empty($siteData['products'])) {
                foreach ($siteData['products'] as $p) {
                    $html .= '
                        <tr>
                            <td>' . esc_html($p['name']) . '</td>
                            <td style="text-align:right;">' . number_format($p['qty']) . '</td>
                        </tr>
                    ';
                }
            } else {
                $html .= '
                    <tr>
                        <td colspan="2" style="text-align:center; color:#777;">No products sold</td>
                    </tr>
                ';
            }

            $html .= '
                <tr class="total-site">
                    <td colspan="2">Total of quantity: ' . number_format($siteData['total']) . '</td>
                </tr>
                <tr class="total-site">
                    <td colspan="2">Total of revenue: ' . number_format($siteData['total_revenue']) . '$</td>
                </tr>
                <tr class="total-site">
                    <td colspan="2">Total of order: ' . number_format($siteData['total_orders']) . '</td>
                </tr>
            </table>
            </div>';
        }

        $html .= '
        <table>
            <tr class="total-all">
                <td style="font-size:18px;">TOTAL SALES QUANTITY</td>
                <td style="text-align:right; font-size:18px;">' . number_format($total_all_sites) . '</td>
            </tr>
            <tr class="total-all">
                <td style="font-size:18px;">TOTAL REVENUE</td>
                <td style="text-align:right; font-size:18px;">' . number_format($total_revenue_all) . '$</td>
            </tr>
            <tr class="total-all">
                <td style="font-size:18px;">TOTAL ORDER</td>
                <td style="text-align:right; font-size:18px;">' . number_format($total_orders_all) . '</td>
            </tr>
        </table>
        ';

        $html .= '</body></html>';

        return $html;
    }

    // fearute send email product qty statistics for all sites
    function send_email_product_qty_statics()
    {
        $to = 'test@gmail.com';
        // current site
        $sites = get_sites();

        // create value to save report data and totals statics
        $report = [];
        $total_all_sites = 0;
        $total_revenue_all = 0;
        $total_orders_all = 0;

        // loop each site to get product qty statistics
        foreach ($sites as $site) {

            // switch to each site
            switch_to_blog($site->blog_id);
            $site_name = get_bloginfo('name');

            // get data to function product_qty_sta_for_each_site
            $data = $this->product_qty_sta_for_each_site();

            // calculate totals and list of report products
            $site_total = array_sum(array_column($data['products'], 'qty'));
            $total_all_sites += $site_total;
            $total_revenue_all += $data['total_revenue'];
            $total_orders_all += $data['total_orders'];

            // add data to report
            $report[] = [
                'site' => $site_name,
                'total' => $site_total,
                'total_revenue' => $data['total_revenue'],
                'total_orders' => $data['total_orders'],
                'products' => array_values($data['products'])
            ];

            restore_current_blog();
        }

        // send email with report
        $subject = 'Thống kê sản phẩm bán được';
        $body = $this->generate_multisite_sales_report_html($report, $total_all_sites, $total_revenue_all, $total_orders_all);

        send_email_facade::send_mail($to, $subject, $body);
    }
}
