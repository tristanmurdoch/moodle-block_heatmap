M.block_heatmap = {
    config: 0,
    min: 0,
    max: 0,
    diff: 0,
    numColours: 5,
    toggleState: true,

    initHeatmap: function (YUIObject, config, min, max, toggledon) {
        this.config = JSON.parse(config);
        this.min = min;
        this.max = max;
        this.diff = max - min;
        this.toggleState = toggledon == 1;
        if (this.toggleState) {
            this.showHeatmap();
        }
    },

    showHeatmap: function () {
        var module;
        var weight;
        var info;
        for (var i = 0; i < this.config.length; i++) {
            module = document.getElementById('module-' + this.config[i].cmid);
            weight = parseInt(this.config[i].numviews / this.diff * this.numColours - 1);
            if (module) {
                module.className += ' block_heatmap_heat_' + weight;
                info = '<div class="block_heatmap_view_count">';
                info += M.str.block_heatmap.views;
                info += '&nbsp;';
                info += this.config[i].numviews;
                info += ', &nbsp;';
                info += M.str.block_heatmap.distinctusers;
                info += '&nbsp;';
                info += this.config[i].distinctusers;
                info += '</div>';
                module.innerHTML = module.innerHTML + info;
            }
        }
    },

    hideHeatmap: function () {
        for (var i = 0; i < this.config.length; i++) {
            module = document.getElementById('module-' + this.config[i].cmid);
            if (module) {
                module.className = module.className.replace(/ block_heatmap_heat_(\d)/, '');
                module.removeChild(module.getElementsByClassName('block_heatmap_view_count')[0]);
            }
        }
    },

    toggleHeatmap: function () {
        this.toggleState = !this.toggleState;
        if(this.toggleState) {
            this.showHeatmap();
        }
        else {
            this.hideHeatmap();
        }
        M.util.set_user_preference('heatmaptogglestate', this.toggleState);
    }
};