Vagrant.configure(2) do |config|

    config.vm.box = "Debian 8.1 (Jessie) amd64"

    config.vm.network "private_network", ip: "192.168.33.18"

    config.vm.synced_folder ".", "/vagrant", :mount_options => [ 'dmode=775', 'fmode=775' ]

    config.vm.provision "shell" do |s|
        s.path = "vagrant-provision.sh"
        s.args = [
            "madokami-vm", # Hostname
            "madokami" # DB name
        ]
    end

    config.vm.provider "virtualbox" do |vb|
        # Display the VirtualBox GUI when booting the machine
        # vb.gui = true
    end
end