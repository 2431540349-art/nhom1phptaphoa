<?php

function get_local_faq(){
    return [
        ['keywords'=>['giờ mở cửa','mở cửa','giờ làm việc'],'answer'=>"Cửa hàng mở cửa từ 7:30 sáng đến 9:00 tối các ngày trong tuần. Ngày lễ có thể thay đổi, vui lòng gọi trước để xác nhận."],
        ['keywords'=>['địa chỉ','ở đâu','địa chỉ cửa hàng'],'answer'=>"Chúng tôi ở 123 Đường Chính, Quận H, Thành phố X. Bạn cũng có thể xem bản đồ trên trang liên hệ để biết chỉ đường chính xác."],
        ['keywords'=>['phương thức thanh toán','thanh toán','thanh toán bằng'],'answer'=>"Chúng tôi chấp nhận tiền mặt khi giao hàng, chuyển khoản ngân hàng, và thanh toán online qua VNPay. Nếu bạn cần hóa đơn, vui lòng liên hệ hỗ trợ."],
        ['keywords'=>['giao hàng','vận chuyển','ship'],'answer'=>"Giao hàng trong ngày cho khu vực nội thành (phí tùy khoảng cách). Thời gian giao thường 2-5 ngày với khu vực ngoại thành. Chi tiết phí sẽ hiển thị khi bạn tiến hành thanh toán."],
        ['keywords'=>['đổi trả','bảo hành','hoàn trả'],'answer'=>"Chúng tôi nhận đổi trả trong vòng 7 ngày kể từ ngày nhận hàng đối với sản phẩm lỗi hoặc không đúng mô tả. Vui lòng giữ hóa đơn và bao bì. Liên hệ hotline để được hướng dẫn cụ thể."],
        ['keywords'=>['mã đơn','trạng thái đơn','theo dõi đơn'],'answer'=>"Bạn có thể cung cấp mã đơn hàng tại đây hoặc vào mục 'Đơn hàng' trong tài khoản để xem trạng thái. Nếu bạn không có tài khoản, gửi mã đơn cho chúng tôi qua chat hoặc email hỗ trợ."],
        ['keywords'=>['hello','xin chào','chào'],'answer'=>"Xin chào! Tôi có thể giúp gì cho bạn hôm nay? Bạn có thể hỏi về giờ mở cửa, địa chỉ, phương thức thanh toán hoặc trạng thái đơn hàng."],
        ['keywords'=>['giờ mở cửa','mở cửa','giờ làm việc'],'answer'=>"Cửa hàng mở cửa từ 7:30 sáng đến 9:00 tối các ngày trong tuần. Ngày lễ có thể thay đổi, vui lòng gọi trước để xác nhận."],
        ['keywords'=>['địa chỉ','ở đâu','địa chỉ cửa hàng'],'answer'=>"Chúng tôi ở 123 Đường Chính, Quận H, Thành phố X. Bạn cũng có thể xem bản đồ trên trang liên hệ để biết chỉ đường chính xác."],
        ['keywords'=>['phương thức thanh toán','thanh toán','thanh toán bằng'],'answer'=>"Chúng tôi chấp nhận tiền mặt khi giao hàng, chuyển khoản ngân hàng, và thanh toán online qua VNPay. Nếu bạn cần hóa đơn, vui lòng liên hệ hỗ trợ."],
        ['keywords'=>['giao hàng','vận chuyển','ship'],'answer'=>"Giao hàng trong ngày cho khu vực nội thành (phí tùy khoảng cách). Thời gian giao thường 2-5 ngày với khu vực ngoại thành. Chi tiết phí sẽ hiển thị khi bạn tiến hành thanh toán."],
        ['keywords'=>['đổi trả','bảo hành','hoàn trả'],'answer'=>"Chúng tôi nhận đổi trả trong vòng 7 ngày kể từ ngày nhận hàng đối với sản phẩm lỗi hoặc không đúng mô tả. Vui lòng giữ hóa đơn và bao bì. Liên hệ hotline để được hướng dẫn cụ thể."],
        ['keywords'=>['mã đơn','trạng thái đơn','theo dõi đơn'],'answer'=>"Bạn có thể cung cấp mã đơn hàng tại đây hoặc vào mục 'Đơn hàng' trong tài khoản để xem trạng thái. Nếu bạn không có tài khoản, gửi mã đơn cho chúng tôi qua chat hoặc email hỗ trợ."],
        ['keywords'=>['hello','xin chào','chào','tư vấn'],'answer'=>"Xin chào! Tôi có thể giúp gì cho bạn hôm nay? Bạn có thể hỏi về giờ mở cửa, địa chỉ, phương thức thanh toán hoặc trạng thái đơn hàng."],
        ['keywords'=>['hôm nay mấy giờ mở cửa'], 'answer'=>"Cửa hàng mở cửa từ 7:30 sáng đến 9:00 tối các ngày trong tuần. Ngày lễ có thể thay đổi, vui lòng gọi trước để xác nhận."],
        ['keywords'=>['mấy giờ đóng cửa'], 'answer'=>"Cửa hàng đóng cửa lúc 9:00 tối mỗi ngày. Ngày lễ có thể thay đổi, vui lòng gọi trước để xác nhận."],
        ['keywords'=>['cửa hàng có mở cửa chủ nhật không'], 'answer'=>"Cửa hàng mở cửa các ngày trong tuần, bao gồm cả Chủ Nhật."],
        ['keywords'=>['giờ làm việc ngày lễ'], 'answer'=>"Ngày lễ có thể thay đổi giờ làm việc. Vui lòng gọi trước để xác nhận giờ mở cửa chính xác."],
        ['keywords'=>['cho tôi xin địa chỉ'], 'answer'=>"Chúng tôi ở 123 Đường Chính, Quận H, Thành phố X. Bạn cũng có thể xem bản đồ trên trang liên hệ để biết chỉ đường chính xác."],
        ['keywords'=>['cửa hàng gần đây'], 'answer'=>"Cửa hàng chính của chúng tôi nằm tại 123 Đường Chính, Quận H, Thành phố X."],
        ['keywords'=>['chỉ đường tới cửa hàng'], 'answer'=>"Bạn có thể xem bản đồ trên trang liên hệ để biết chỉ đường chính xác đến 123 Đường Chính, Quận H, Thành phố X."],
        ['keywords'=>['có thanh toán visa không'], 'answer'=>"Hiện tại, chúng tôi chấp nhận tiền mặt khi giao hàng, chuyển khoản ngân hàng, và thanh toán online qua VNPay."],
        ['keywords'=>['thanh toán khi nhận hàng'], 'answer'=>"Chúng tôi chấp nhận **tiền mặt khi giao hàng (COD)**."],
        ['keywords'=>['chuyển khoản thanh toán'], 'answer'=>"Bạn có thể thanh toán bằng **chuyển khoản ngân hàng**. Thông tin tài khoản sẽ được cung cấp khi xác nhận đơn hàng."],
        ['keywords'=>['có xuất hóa đơn VAT không'], 'answer'=>"Chúng tôi có thể xuất hóa đơn VAT. Vui lòng liên hệ hỗ trợ và cung cấp thông tin công ty của bạn."],
        ['keywords'=>['phí giao hàng bao nhiêu'], 'answer'=>"Phí giao hàng tùy thuộc vào khoảng cách và khu vực. Chi tiết phí sẽ hiển thị khi bạn tiến hành thanh toán."],
        ['keywords'=>['thời gian giao hàng nội thành'], 'answer'=>"Chúng tôi hỗ trợ **giao hàng trong ngày** cho khu vực nội thành."],
        ['keywords'=>['bao lâu thì nhận được hàng'], 'answer'=>"Thời gian giao thường 2-5 ngày với khu vực ngoại thành, và giao trong ngày cho nội thành."],
        ['keywords'=>['có miễn phí vận chuyển không'], 'answer'=>"Chúng tôi có các chương trình miễn phí vận chuyển cho đơn hàng đạt giá trị nhất định. Vui lòng xem thông báo khuyến mãi trên trang chủ."],
        ['keywords'=>['chính sách đổi trả'], 'answer'=>"Chúng tôi nhận đổi trả trong vòng **7 ngày** kể từ ngày nhận hàng đối với sản phẩm lỗi hoặc không đúng mô tả."],
        ['keywords'=>['làm sao để đổi hàng'], 'answer'=>"Vui lòng giữ hóa đơn và bao bì, sau đó liên hệ **hotline** để được hướng dẫn cụ thể quy trình đổi trả."],
        ['keywords'=>['thời gian bảo hành'], 'answer'=>"Thời gian bảo hành tùy thuộc vào từng sản phẩm cụ thể. Vui lòng kiểm tra trên trang sản phẩm hoặc liên hệ hỗ trợ để biết chi tiết."],
        ['keywords'=>['sản phẩm bị lỗi'], 'answer'=>"Chúng tôi nhận đổi trả trong vòng 7 ngày đối với sản phẩm lỗi. Vui lòng liên hệ ngay để chúng tôi hỗ trợ bạn."],
        ['keywords'=>['đơn hàng của tôi đang ở đâu'], 'answer'=>"Bạn có thể cung cấp mã đơn hàng tại đây hoặc vào mục 'Đơn hàng' trong tài khoản để xem trạng thái."],
        ['keywords'=>['kiểm tra đơn hàng'], 'answer'=>"Vui lòng cung cấp mã đơn hàng cho chúng tôi qua chat hoặc email hỗ trợ để kiểm tra trạng thái."],
        ['keywords'=>['theo dõi đơn'], 'answer'=>"Bạn có thể cung cấp mã đơn hàng tại đây hoặc vào mục 'Đơn hàng' trong tài khoản để xem trạng thái."],
        ['keywords'=>['chưa nhận được hàng'], 'answer'=>"Nếu đã quá thời gian dự kiến, vui lòng cung cấp mã đơn hàng để chúng tôi kiểm tra ngay tình trạng đơn hàng của bạn."],
        ['keywords'=>['cảm ơn'], 'answer'=>"Rất vui được phục vụ bạn! Nếu bạn có bất kỳ câu hỏi nào khác, đừng ngần ngại cho tôi biết nhé."],
        ['keywords'=>['liên hệ'], 'answer'=>"Bạn có thể gọi đến hotline: XXX-YYYY-ZZZZ, gửi email đến support@email.com, hoặc chat trực tiếp tại đây."],
        ['keywords'=>['tôi muốn nói chuyện với nhân viên'], 'answer'=>"Tôi sẽ chuyển cuộc trò chuyện này đến một nhân viên hỗ trợ ngay lập tức. Vui lòng đợi trong giây lát."],
        ['keywords'=>['chúc một ngày tốt lành'], 'answer'=>"Cảm ơn bạn! Chúc bạn cũng có một ngày tốt lành!"],
        ['keywords'=>['sản phẩm mới'], 'answer'=>"Bạn có thể xem các sản phẩm mới nhất của chúng tôi tại mục 'Sản phẩm mới' trên trang chủ."],
        ['keywords'=>['khuyến mãi'], 'answer'=>"Chúng tôi có các chương trình khuyến mãi và ưu đãi đặc biệt thường xuyên. Vui lòng truy cập mục 'Khuyến mãi' để biết thêm chi tiết."],
        ['keywords'=>['làm thế nào để đặt hàng'], 'answer'=>"Bạn có thể thêm sản phẩm vào giỏ hàng và làm theo các bước thanh toán trên trang web của chúng tôi."],
        ['keywords'=>['tôi nên mua loại nào','loại nào tốt hơn','tư vấn mua'], 'answer'=>"Bạn đang tìm kiếm sản phẩm gì ạ? (Ví dụ: bột giặt, dầu gội, sữa). Tôi sẽ giúp bạn chọn loại phù hợp với nhu cầu và túi tiền của bạn."],
        ['keywords'=>['có cái nào rẻ hơn không','sản phẩm thay thế'], 'answer'=>"Bạn muốn tìm sản phẩm thay thế cho mặt hàng nào? Chúng tôi luôn có các lựa chọn tiết kiệm và chất lượng tương đương."],
        ['keywords'=>['sản phẩm bán chạy nhất'], 'answer'=>"Sản phẩm **bán chạy nhất** trong mục này là [Tên Sản Phẩm Hot]. Đây là lựa chọn được nhiều khách hàng tin dùng vì chất lượng và giá cả phải chăng."],
        ['keywords'=>['nước ngọt nào ít đường','nước giải khát healthy'], 'answer'=>"Chúng tôi có các loại **nước suối, nước ép không đường hoặc ít đường** (ví dụ: Trà Xanh 0 độ). Bạn có muốn thử không ạ?"],
        ['keywords'=>['bia nào ngon','bia nào lạnh'], 'answer'=>"Chúng tôi có đầy đủ các loại bia phổ biến như **Tiger, Heineken, SaiGon**. Tất cả đều được giữ lạnh sẵn sàng phục vụ."],
        ['keywords'=>['sữa tươi nào cho trẻ em','sữa cho người lớn'], 'answer'=>"Chúng tôi có sữa tươi **Vinamilk** và **TH True Milk** (loại có đường, không đường). Vui lòng cho biết bạn cần loại nào cho bé hay cho người lớn ạ?"],
        ['keywords'=>['mì gói nào ngon','mì gói cay'], 'answer'=>"Các loại **mì gói Hảo Hảo** và **Omachi** được khách hàng ưa chuộng nhất. Nếu bạn thích ăn cay, hãy thử mì **Kokomi chua cay**!"],
        ['keywords'=>['bột giặt loại nào thơm','dùng cho máy giặt'], 'answer'=>"Bạn nên dùng **Bột giặt Omo Matic** hoặc **Surf Matic**. Cả hai đều có mùi thơm lâu và được thiết kế đặc biệt cho máy giặt."],
        ['keywords'=>['dầu gội trị gàu','dầu gội mượt tóc'], 'answer'=>"Chúng tôi có **Clear** và **Head & Shoulders** chuyên trị gàu. Nếu bạn cần loại giúp tóc mượt hơn, hãy chọn **SunSilk** hoặc **Rejoice**."],
        ['keywords'=>['tã em bé loại nào tốt','bỉm nào chống hằn'], 'answer'=>"Chúng tôi có tã/bỉm của **Bobby** và **Huggies**. Cả hai đều có dòng chống hằn và thấm hút tốt. Bạn cần size M, L hay XL?"],
        ['keywords'=>['kem đánh răng loại nào trắng'], 'answer'=>"Bạn có thể thử các dòng kem đánh răng như **CloseUp** hoặc **P/S Trắng Răng**. Chúng có công thức làm trắng hiệu quả."],
        ['keywords'=>['dao cạo râu','băng vệ sinh'], 'answer'=>"Vâng, chúng tôi có bán **dao cạo râu Gillette** và đầy đủ các loại **băng vệ sinh Diana, Kotex**."],
        ['keywords'=>['đồ ăn vặt nào mới','snack nào hot'], 'answer'=>"Các loại snack **Oishi** và **Poca** luôn được ưa chuộng. Hiện tại, chúng tôi có thêm nhiều vị mới rất hấp dẫn."],
        ['keywords'=>['bánh kẹo nào mới','snack nào ngon'], 'answer'=>"Chúng tôi có các loại **Bánh kẹo** và **Snack** mới nhất của Orion, Kinh Đô. Bạn có muốn thử kẹo **sữa mềm Alpenliebe** không?"],
        ['keywords'=>['bánh mì tươi','bánh mì ngọt'], 'answer'=>"Chúng tôi có **Bánh mì tươi** được giao hàng ngày. Bạn muốn loại **bánh mì sandwich** hay **bánh mì ngọt**?"],
        ['keywords'=>['mì gói nào ít dầu','cháo ăn liền'], 'answer'=>"Bạn có thể chọn **Mì ăn liền Tiến Vua** (ít chiên) hoặc các loại **Cháo, Phở ăn liền** Vifon, Acecook rất tiện lợi."],
        ['keywords'=>['đồ hộp','cá hộp','thịt hộp'], 'answer'=>"Chúng tôi có đủ các loại **Đồ hộp** như Cá ngừ dầu, Thịt heo hai lát, và Pate gan. Tất cả đều là hàng chất lượng tốt."],
        ['keywords'=>['đồ đông lạnh','mua xúc xích','chả giò'], 'answer'=>"Khu vực **Đồ đông lạnh** có nhiều loại **xúc xích, chả giò, cá viên** và kem. Tất cả đều được bảo quản đúng nhiệt độ."],
        ['keywords'=>['dầu ăn nào tốt cho sức khỏe','dầu ăn olive'], 'answer'=>"Chúng tôi có **Dầu ăn Tường An** (thông dụng) và **Dầu đậu nành/Dầu gạo lứt** (ít béo hơn)."],
        ['keywords'=>['nước mắm ngon','nước tương'], 'answer'=>"Bạn có thể chọn **Nước mắm truyền thống** (Phú Quốc) hoặc các loại **Nước tương** Maggi, Chin-su tùy khẩu vị."],
        ['keywords'=>['gạo loại nào dẻo','gạo nở'], 'answer'=>"Chúng tôi có **Gạo Tẻ Thơm** (dẻo, mềm cơm) hoặc **Gạo Nàng Hương** (nở vừa). Vui lòng cho biết bạn muốn mua bao nhiêu kg?"],
        ['keywords'=>['muối','đường','bột ngọt'], 'answer'=>"Vâng, các loại **Gia vị** cơ bản như Muối, Đường, Bột ngọt, Hạt nêm đều có sẵn tại khu vực kệ gia vị."],
        ['keywords'=>['kem đánh răng','bàn chải','dầu gội'], 'answer'=>"Các sản phẩm **Chăm sóc cá nhân** như Kem đánh răng (P/S, Colgate), Dầu gội (Clear, SunSilk) luôn có sẵn. Bạn tìm nhãn hiệu nào ạ?"],
        ['keywords'=>['bột giặt loại nào thơm','nước giặt đậm đặc'], 'answer'=>"Chúng tôi có **Bột giặt** Omo, Tide và **Nước giặt** Ariel đậm đặc, giúp quần áo sạch và thơm lâu."],
        ['keywords'=>['nước rửa chén','nước lau sàn'], 'answer'=>"Các loại **Nước rửa chén** Sunlight, Mỹ Hảo và **Nước lau nhà** Gift, Lix có sẵn để bạn lựa chọn."],
        ['keywords'=>['mua khăn giấy','giấy vệ sinh','bàn chải đánh răng'], 'answer'=>"Vâng, **Đồ dùng gia đình** như Giấy ăn, Giấy vệ sinh, Tăm, Túi rác đều có đủ trên kệ."],
        ['keywords'=>['rau củ tươi','trái cây'], 'answer'=>"Chúng tôi nhập **Rau củ và Trái cây** tươi mới mỗi buổi sáng. Bạn muốn mua loại rau gì ạ (Cà chua, Dưa chuột, Cải)?"],
        ['keywords'=>['trứng gà','trứng vịt','trứng tươi'], 'answer'=>"Chúng tôi chỉ bán **Trứng tươi** được kiểm tra chất lượng. Bạn cần Trứng gà hay Trứng vịt?"],
        ['keywords'=>['thịt heo','thịt bò','hải sản đông lạnh'], 'answer'=>"Chúng tôi có **Thịt heo tươi** và một số **Thịt - Hải sản** đông lạnh được đóng gói cẩn thận."],
        ['keywords'=>['sữa tươi','sữa chua','sữa đặc'], 'answer'=>"Khu vực **Sữa và sản phẩm từ sữa** có Sữa tươi (Vinamilk, TH), Sữa chua, Váng sữa và Sữa đặc."],
        ['keywords'=>['văn phòng phẩm','bút viết','tập học sinh'], 'answer'=>"Chúng tôi có bán các loại **Văn phòng phẩm** cơ bản như Bút, Tập, Giấy A4. Bạn cần loại nào?"],
        ['keywords'=>['thuốc đau đầu','cảm cúm','thuốc cơ bản'], 'answer'=>"Chúng tôi có bán các loại **Thuốc không kê đơn** và **Y tế** cơ bản như thuốc cảm, thuốc đau đầu, băng gạc. Vui lòng hỏi nhân viên để được hướng dẫn."],
    ];
}

function find_local_faq_answer($message){
    $msg = mb_strtolower(trim($message), 'UTF-8');
    $faqs = get_local_faq();
    foreach($faqs as $f){
        foreach($f['keywords'] as $kw){
            if ($kw === '') continue;
            if (mb_stripos($msg, $kw, 0, 'UTF-8') !== false) {
                return $f['answer'];
            }
        }
    }
    return null;
}

