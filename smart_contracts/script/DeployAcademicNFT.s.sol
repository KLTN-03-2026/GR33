// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import {Script, console} from "forge-std/Script.sol";
import {AcademicNFT} from "../src/AcademicNFT.sol";

contract DeployAcademicNFT is Script {
    function run() public {
        // Đọc Private Key từ biến môi trường
        uint256 deployerPrivateKey = vm.envUint("PRIVATE_KEY");
        
        // Bắt đầu ghi lại các giao dịch
        vm.startBroadcast(deployerPrivateKey);

        // Lấy địa chỉ ví từ Private Key để làm Owner
        address deployerAddress = vm.addr(deployerPrivateKey);
        
        // Đọc địa chỉ Admin được ủy quyền từ biến môi trường (Ví Giám đốc)
        address authorizedAdmin = vm.envAddress("AUTHORIZED_ADMIN");
        
        // Triển khai hợp đồng với 2 tham số
        AcademicNFT academicNFT = new AcademicNFT(deployerAddress, authorizedAdmin);

        console.log("AcademicNFT deployed at:", address(academicNFT));
        console.log("Owner address:", deployerAddress);
        console.log("Authorized Admin:", authorizedAdmin);

        // Kết thúc ghi giao dịch
        vm.stopBroadcast();
    }
}
