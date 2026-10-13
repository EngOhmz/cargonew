<?php

namespace App\Http\Controllers\restaurant;

use App\Http\Controllers\Controller;
use App\Models\restaurant\Restaurant;
use App\Models\Inventory\Location;
use App\Models\restaurant\Menu;
use App\Models\restaurant\MenuComponent;
use App\Models\restaurant\Table;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;


class MenuItemController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
           $index=Menu::all();
           $type="";
            return view('restaurant.menu-item.index', compact('index','type'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data['name']=$request->name;
        $data['price']=$request->price;
        $data['status']='1';
        $data['user_id']=auth()->user()->id;
        $menu= Menu::create($data);

        $nameArr =$request->component ;
  
       
        
           if(!empty($nameArr)){
               for($i = 0; $i < count($nameArr); $i++){
                   if(!empty($nameArr[$i])){
                       $items = array(
                           'name' => $nameArr[$i],
                              'order_no' => $i,
                              'user_id'=>auth()->user()->id,
                           'menu_id' =>$menu->id);
       
                       MenuComponent::create($items);  ;
       
       
                   }
               }
           }    
       


        Toastr::success('New Menu Created Successfully','Success');
        return redirect(route('menu-items.index'));
    }

   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
         $menu=Menu::find($id);
        $items = MenuComponent::where('menu_id',$id)->get();
        return view('restaurant.menu-item.show',compact('items','menu'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data=Menu::find($id);
        $items = MenuComponent::where('menu_id',$id)->get(); 
        $type=""; 
        return view('restaurant.menu-item.index', compact('data','items','id','type'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $menu=Menu::find($id);

        if($request->type == ''){

        $data['name']=$request->name;
        $data['price']=$request->price;
        $menu->update($data);

        $nameArr =$request->item_name ;
        $remArr = $request->removed_id ;
        $expArr = $request->saved_id ;
       
       
         if (!empty($remArr)) {
            for($i = 0; $i < count($remArr); $i++){
               if(!empty($remArr[$i])){        
                  MenuComponent::where('id',$remArr[$i])->delete();        
                   }
               }
           }

           if(!empty($nameArr)){
               for($i = 0; $i < count($nameArr); $i++){
                   if(!empty($nameArr[$i])){
                       $items = array(
                        'name' => $nameArr[$i],
                        'order_no' => $i,
                        'user_id'=>auth()->user()->id,
                         'menu_id' =>$id);
                        
                           if(!empty($expArr[$i])){
                            MenuComponent::where('id',$expArr[$i])->update($items);  
      
      }
                          else{
                         MenuComponent::create($items);  
      
      }

                          
       
       
                   }
               }
           }    
       

        Toastr::success('Menu Updated Successfully','Success');
        return redirect(route('menu-items.index'));
        }


        else{


            $data['name']=$request->name;
            $data['price']=$request->price;
            $data['status']='1';
            $menu->update($data);
    
            $nameArr =$request->item_name ;
            $remArr = $request->removed_id ;
            $expArr = $request->saved_id ;
           
           
             if (!empty($remArr)) {
                for($i = 0; $i < count($remArr); $i++){
                   if(!empty($remArr[$i])){        
                      MenuComponent::where('id',$remArr[$i])->delete();        
                       }
                   }
               }
    
               if(!empty($nameArr)){
                   for($i = 0; $i < count($nameArr); $i++){
                       if(!empty($nameArr[$i])){
                           $items = array(
                            'name' => $nameArr[$i],
                            'order_no' => $i,
                            'user_id'=>auth()->user()->id,
                             'menu_id' =>$id);
                            
                               if(!empty($expArr[$i])){
                                MenuComponent::where('id',$expArr[$i])->update($items);  
          
          }
                              else{
                             MenuComponent::create($items);  
          
          }
    
                              
           
           
                       }
                   }
               }    
           
    
            Toastr::success('Status Changed Successfully','Success');
            return redirect(route('menu-items.index'));

        }
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        MenuComponent::where('menu_id', $id)->delete();
        $menu = Menu::find($id);
        $menu->delete();

        Toastr::success('Menu Deleted Successfully','Success');
        return redirect(route('menu-items.index'));
    }

    public function change_status($id)
    {
        //
        
        $menu = Menu::find($id);
        if($menu->status == '1'){
        $item['status'] = 0;
        $menu->update($item);

        Toastr::success('Status Changed Successfully','Success');
        return redirect(route('menu-items.index'));
        }

        elseif($menu->status == '0'){
            $data=Menu::find($id);
            $items = MenuComponent::where('menu_id',$id)->get();
            $type="status"; 
             return view('restaurant.menu-item.index', compact('data','items','id','type'));
        }
    }
 
}
