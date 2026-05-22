// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/cryptography/ECDSA.sol";
import "@openzeppelin/contracts/utils/cryptography/MessageHashUtils.sol";

contract AcademicNFT is ERC721, ERC721URIStorage, Ownable {
    using ECDSA for bytes32;

    uint256 public _nextTokenId;
    // Lưu trữ các Header Hash để tránh việc đúc trùng lặp một hồ sơ
    mapping(bytes32 => bool) public recordHashes;

    // Lưu trữ chuỗi JSON Metadata chứa thông tin chi tiếp của hồ sơ (Điểm, Chứng chỉ, Dự án)
    mapping(uint256 => string) public tokenMetadata;

    // Lưu trữ mã ID của nhân viên (từ Backend) thực hiện thao tác ký/đúc cho mỗi Token
    mapping(uint256 => uint256) public operatorIds;
    
    // Ánh xạ từ Token ID sang Mã băm hồ sơ để hỗ trợ xóa khi Burn
    mapping(uint256 => bytes32) public tokenIdToDataHash;
    
    // Địa chỉ ví Admin (Giám đốc) duy nhất được phép ký số
    address public authorizedAdmin;

    event AcademicRecordMinted(
        address indexed student, 
        uint256 indexed tokenId, 
        bytes32 dataHash,
        uint256 indexed operatorId,
        string metadata
    );

    constructor(address initialOwner, address _authorizedAdmin)
        ERC721("Academic Record NFT", "ARNFT")
        Ownable(initialOwner)
    {
        authorizedAdmin = _authorizedAdmin;
    }

    /**
     * @dev Đúc một NFT hồ sơ học tập mới.
     * @param to Địa chỉ ví của sinh viên sở hữu hồ sơ.
     * @param dataHash Mã băm SHA-256 của hồ sơ (từ Backend).
     * @param tokenUri Đường dấn tới Metadata.
     * @param signature Chữ ký điện tử của Admin.
     * @param operatorId Mã ID của nhân viên thực hiện thao tác.
     * @param metadata Chuỗi JSON chứa dữ liệu chi tiết hồ sơ.
     */
    function safeMint(
        address to,
        bytes32 dataHash,
        string memory tokenUri,
        bytes memory signature,
        uint256 operatorId,
        string memory metadata
    ) public onlyOwner {
        // 1. Kiểm tra xem mã băm này đã được đúc NFT chưa
        require(!recordHashes[dataHash], unicode"Hồ sơ này đã được cấp NFT rồi.");
        
        // 2. Xác thực chữ ký On-chain (Phải khớp với ví Admin đã được ủy quyền)
        bytes32 ethSignedMessageHash = MessageHashUtils.toEthSignedMessageHash(dataHash);
        address signer = ECDSA.recover(ethSignedMessageHash, signature);
        
        require(signer != address(0), unicode"Chữ ký không hợp lệ.");
        require(signer == authorizedAdmin, unicode"Người ký không phải là Admin được ủy quyền.");
        
        // 3. Thực hiện đúc NFT
        uint256 tokenId = _nextTokenId++;
        recordHashes[dataHash] = true;
        tokenIdToDataHash[tokenId] = dataHash; 
        operatorIds[tokenId] = operatorId; 
        tokenMetadata[tokenId] = metadata; // Lưu dữ liệu JSON trực tiếp On-chain
 
        _safeMint(to, tokenId);
        _setTokenURI(tokenId, tokenUri);
        
        emit AcademicRecordMinted(to, tokenId, dataHash, operatorId, metadata);
    }
 
    /**
     * @dev Tiêu hủy NFT vĩnh viễn trên chuỗi.
     * Chỉ Admin (Owner) mới có quyền tiêu hủy NFT khi có yêu cầu thu hồi.
     */
    function burn(uint256 tokenId) public onlyOwner {
        bytes32 dataHash = tokenIdToDataHash[tokenId];
        
        // Xóa các dấu vết dữ liệu để có thể đúc lại nếu cần thiết
        delete recordHashes[dataHash];
        delete tokenIdToDataHash[tokenId];
        delete tokenMetadata[tokenId];
        delete operatorIds[tokenId];
 
        _burn(tokenId);
    }

    // Các hàm Override bắt buộc của Solidity
    function tokenURI(uint256 tokenId)
        public
        view
        override(ERC721, ERC721URIStorage)
        returns (string memory)
    {
        return super.tokenURI(tokenId);
    }

    function supportsInterface(bytes4 interfaceId)
        public
        view
        override(ERC721, ERC721URIStorage)
        returns (bool)
    {
        return super.supportsInterface(interfaceId);
    }
}
