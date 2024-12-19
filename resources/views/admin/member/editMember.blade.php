
    <div class="modal fade" id="editMemberModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action="" id="updateMember"  enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @method('put')
                    <input type="hidden" id='m_id' name="m_id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Member</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                        <div class="form-group">
                            
                            <div class="profile-picture-container">
                                <img id="profilePicPreview" src="{{ asset('storage/profile_pictures/default.png') }}" alt="Profile Picture" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('profile_picture').click();">
                                <input type="file" class="form-control mt-2" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)" style="display: none;">
                            </div>
                        </div>
                            <div class="form-group required">
                                <label class="control-label">Full Name</label>
                                <input type="text" class="form-control" id="update_full_name"
                                    name="full_name"placeholder="Full Name" required>
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Phone</label>
                                <input type="text" class="form-control" id="update_phone"
                                    name="phone"placeholder="251911121314" required>
                            </div>
                            <div class="form-group" id="addEmail">
                                <label class="control-label">Email</label>
                                <input type="text" class="form-control" id="update_email"
                                    name="email"placeholder="Email">
                            </div>
                            <div class="form-group required">
                                <label class="control-label">Gender</label>
                                <select class="form-control" id="update_gender" name="gender">
                                    <option value="Female" id="" Female="">Female</option>
                                    <option value="Male" id="" Male="">Male</option>
                                </select>
                            </div>
                            <hr>
                            <label class="control-label">Address</label>
                            <div class="col-12 row">
                                <div class="form-group required col-6" id="addCity">
                                    <label class="control-label">City</label>
                                    <select class="form-control select2" name="update_city" id="update_city"
                                        name="city" placeholder="City" autocomplete="off" required="true" required>
                                        <option value="">City</option>
                                        <option value="Abomsa">Abomsa</option>
                                        <option value="Adama">Adama</option>
                                        <option value="Addis Ababa">Addis Ababa</option>
                                        <option value="Addis Zemen">Addis Zemen</option>
                                        <option value="Adet">Adet</option>
                                        <option value="Adigrat">Adigrat</option>
                                        <option value="Agaro">Agaro</option>
                                        <option value="Ä€reka">Ä€reka</option>
                                        <option value="Arba Minch">Arba Minch</option>
                                        <option value="Asaita">Asaita</option>
                                        <option value="Assbe Tefera">Assbe Tefera</option>
                                        <option value="Assosa">Assosa</option>
                                        <option value="Assosa">Assosa</option>
                                        <option value="Axum">Axum</option>
                                        <option value="Bahir Dar">Bahir Dar</option>
                                        <option value="Bako">Bako</option>
                                        <option value="Bata">Bata</option>
                                        <option value="Bedele">Bedele</option>
                                        <option value="Bedesa">Bedesa</option>
                                        <option value="Bichena">Bichena</option>
                                        <option value="Bishoftu">Bishoftu</option>
                                        <option value="Boditi">Boditi</option>
                                        <option value="Bonga">Bonga</option>
                                        <option value="Bure">Bure</option>
                                        <option value="Butajira">Butajira</option>
                                        <option value="Debark">Debark</option>
                                        <option value="Debre Birhan">Debre Birhan</option>
                                        <option value="Debre Markos">Debre Markos</option>
                                        <option value="Debre Tabor">Debre Tabor</option>
                                        <option value="Dessie">Dessie</option>
                                        <option value="Dilla">Dilla</option>
                                        <option value="Dire Dawa">Dire Dawa</option>
                                        <option value="Dodola">Dodola</option>
                                        <option value="Dubti">Dubti</option>
                                        <option value="Felege Neway">Felege Neway</option>
                                        <option value="Fiche">Fiche</option>
                                        <option value="Finote Selam">Finote Selam</option>
                                        <option value="Gambela">Gambela</option>
                                        <option value="Gebre Guracha">Gebre Guracha</option>
                                        <option value="Gelemso">Gelemso</option>
                                        <option value="Genet">Genet</option>
                                        <option value="Gimbi">Gimbi</option>
                                        <option value="Ginir">Ginir</option>
                                        <option value="Goba">Goba</option>
                                        <option value="Gondar">Gondar</option>
                                        <option value="Golwayn">Golwayn</option>
                                        <option value="Hagere Hiywet">Hagere Hiywet</option>
                                        <option value="Hagere Maryam">Hagere Maryam</option>
                                        <option value="Harar">Harar</option>
                                        <option value="Hosaaina">Hosaaina</option>
                                        <option value="Inda Silase">Inda Silase</option>
                                        <option value="Jijiga">Jijiga</option>
                                        <option value="Jimma">Jimma</option>
                                        <option value="Jinka">Jinka</option>
                                        <option value="Kahandhale">Kahandhale</option>
                                        <option value="Kemise">Kemise</option>
                                        <option value="Kibre Mengist">Kibre Mengist</option>
                                        <option value="Korem">Korem</option>
                                        <option value="Lasoano">Lasoano</option>
                                        <option value="Maychew">Maychew</option>
                                        <option value="Mek'ele">Mek'ele</option>
                                        <option value="Metahara">Metahara</option>
                                        <option value="Metu">Metu</option>
                                        <option value="Mojo">Mojo</option>
                                        <option value="Nazret">Nazret</option>
                                        <option value="Neefkuceliye">Neefkuceliye</option>
                                        <option value="Nejo">Nejo</option>
                                        <option value="Qorof">Qorof</option>
                                        <option value="Raqo">Raqo</option>
                                        <option value="Robit">Robit</option>
                                        <option value="Sodo">Sodo</option>
                                        <option value="Sebeta">Sebeta</option>
                                        <option value="Shakiso">Shakiso</option>
                                        <option value="Shambu">Shambu</option>
                                        <option value="Shashemene">Shashemene</option>
                                        <option value="Waliso">Waliso</option>
                                        <option value="Wenji">Wenji</option>
                                        <option value="Werota">Werota</option>
                                        <option value="Yabelo">Yabelo</option>
                                        <option value="Yamarugley">Yamarugley</option>
                                        <option value="Yirga Alem">Yirga Alem</option>
                                        <option value="Ziway">Ziway</option>
                                        <option value="Waal">Waal</option>
                                        <option value="Fadhigaradle">Fadhigaradle</option>
                                        <option value="Gedo">Gedo</option>
                                        <option value="Digih Habar Es">Digih Habar Es</option>
                                    </select>
                                </div>
                                <div class="form-group col-6" id="addSubcity">
                                    <label class="control-label">Sub-City</label>
                                    {{-- <select class="form-control select2" id="update_subcity" name="update_subcity"
                                        placeholder="Subcity" autocomplete="off">
                                        <option value="">Subcity</option>
                                        <option value="Addis Ketema">Addis Ketema</option>
                                        <option value="Akaky Kaliti">Akaky Kaliti</option>
                                        <option value="Arada">Arada</option>
                                        <option value="Bole">Bole</option>
                                        <option value="Gullele">Gullele</option>
                                        <option value="Kirkos">Kirkos</option>
                                        <option value="Kolfe Keranio">Kolfe Keranio</option>
                                        <option value="Lideta">Lideta</option>
                                        <option value="Lemi Kura">Lemi Kura</option>
                                        <option value="Nifas Silk-Lafto">Nifas Silk-Lafto</option>
                                        <option value="Yeka">Yeka</option>
                                    </select> --}}
                                    <input type="text" class="form-control" id="update_subcity"
                                        name="update_subcity"placeholder="Subcity">
                                </div>
                            </div>
                            <div class="col-12 row">
                                <div class="form-group col-6" id="addWoreda">
                                    <label class="control-label">Woreda</label>
                                    <input type="text" class="form-control" id="update_woreda"
                                        name="update_woreda"placeholder="Woreda">
                                </div>
                                <div class="form-group col-6" id="addHousenumber">
                                    <label class="control-label">House Number</label>
                                    <input type="text" class="form-control" id="update_housenumber"
                                        name="update_housenumber"placeholder="House Number">
                                </div>
                                <div class="form-group required col-12" id="addLocation">
                                    <label class="control-label">Specific Location</label>
                                    <input type="text" class="form-control" id="update_location"
                                        name="update_location"placeholder="Location" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2" id="updateSubmitBtn">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rateMemberModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form role="form" method="post" class="form-horizontal form-group" action=""
                    id="rateMember">
                    {{ csrf_field() }}
                    @method('put')
                    <input type="hidden" id='m_id' name="m_id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Rate Member</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group required">
                                <label class="control-label">Rating</label>
                                <input type="text" class="form-control" id="rating"
                                    name="rating"placeholder="Rating" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary mr -2" id="updateSubmitBtn">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('profilePicPreview').src = e.target.result;
    }
    reader.readAsDataURL(file);
}
</script>