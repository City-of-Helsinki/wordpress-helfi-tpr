<?php 
  use CityOfHelsinki\WordPress\TPR\Cpt as Plugin;
  ?>
    <nav class="nav-tab-wrapper">
      <a href="#" class="nav-tab nav-tab-active" data-container="finnish-tpr-data"><?php _e('Finnish', 'helsinki-tpr') ?></a>
      <a href="#" class="nav-tab" data-container="english-tpr-data"><?php _e('English', 'helsinki-tpr') ?></a>
      <a href="#" class="nav-tab" data-container="swedish-tpr-data"><?php _e('Swedish', 'helsinki-tpr') ?></a>
    </nav>

    <div class="post-tpr-data finnish-tpr-data active">
      <?php
        Plugin\render_unit_data_row(__('Name', 'helsinki-tpr'), array($data->name('fi')));
        Plugin\render_unit_data_row(__('Image', 'helsinki-tpr'), array($data->html_img('fi')), 'tpr-img');
        Plugin\render_unit_data_row(__('Phone', 'helsinki-tpr'), array($data->phone()));
        Plugin\render_unit_data_row(__('Email', 'helsinki-tpr'), array($data->email()));
        Plugin\render_unit_data_row(__('Website URL', 'helsinki-tpr'), array($data->website_url('fi')));
        Plugin\render_unit_data_row(__('Street Address', 'helsinki-tpr'), array($data->street_address('fi'), $data->address_zip(), $data->address_city('fi')));
        Plugin\render_unit_data_row(__('Postal Address', 'helsinki-tpr'), array($data->postal_address('fi')));
        Plugin\render_unit_data_row(__('Open hours', 'helsinki-tpr'), $data->open_hours('fi'));
        Plugin\render_unit_data_row(__('Additional information', 'helsinki-tpr'), $data->additional_info('fi'));
      ?>
    </div>
    <div class="post-tpr-data english-tpr-data">
      <?php
        Plugin\render_unit_data_row(__('Name', 'helsinki-tpr'), array($data->name('en')));
        Plugin\render_unit_data_row(__('Image', 'helsinki-tpr'), array($data->html_img('en')), 'tpr-img');
        Plugin\render_unit_data_row(__('Phone', 'helsinki-tpr'), array($data->phone()));
        Plugin\render_unit_data_row(__('Email', 'helsinki-tpr'), array($data->email()));
        Plugin\render_unit_data_row(__('Website URL', 'helsinki-tpr'), array($data->website_url('en')));
        Plugin\render_unit_data_row(__('Street Address', 'helsinki-tpr'), array($data->street_address('en'), $data->address_zip(), $data->address_city('en')));
        Plugin\render_unit_data_row(__('Postal Address', 'helsinki-tpr'), array($data->postal_address('en')));
        Plugin\render_unit_data_row(__('Open hours', 'helsinki-tpr'), $data->open_hours('en'));
        Plugin\render_unit_data_row(__('Additional information', 'helsinki-tpr'), $data->additional_info('en'));
      ?>
    </div>
    <div class="post-tpr-data swedish-tpr-data">
      <?php
        Plugin\render_unit_data_row(__('Name', 'helsinki-tpr'), array($data->name('sv')));
        Plugin\render_unit_data_row(__('Image', 'helsinki-tpr'), array($data->html_img('sv')), 'tpr-img');
        Plugin\render_unit_data_row(__('Phone', 'helsinki-tpr'), array($data->phone()));
        Plugin\render_unit_data_row(__('Email', 'helsinki-tpr'), array($data->email()));
        Plugin\render_unit_data_row(__('Website URL', 'helsinki-tpr'), array($data->website_url('sv')));
        Plugin\render_unit_data_row(__('Street Address', 'helsinki-tpr'), array($data->street_address('sv'), $data->address_zip(), $data->address_city('sv')));
        Plugin\render_unit_data_row(__('Postal Address', 'helsinki-tpr'), array($data->postal_address('sv')));
        Plugin\render_unit_data_row(__('Open hours', 'helsinki-tpr'), $data->open_hours('sv'));
        Plugin\render_unit_data_row(__('Additional information', 'helsinki-tpr'), $data->additional_info('sv'));
      ?>
    </div>
  <?php
